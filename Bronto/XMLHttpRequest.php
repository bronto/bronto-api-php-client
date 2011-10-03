<?php
/**
 * Class XMLHttpRequest for PHP 5 only
 * Easiest way of using PHP cURL library, it's uses "XMLHttpRequest" syntax to work with cURL.
 *
 * For support issues please refer to the official web site: http://www.moonlight21.com/class-XMLHttpRequest-php
 *
 * @version $Id: class.XMLHttpRequest.php,v 1.03b 2009/10/16 11:00:45 $
 * @author Moises Lima <mozlima@hotmail.com>
 * @copyright Copyright (c) 2009 Moises Lima (http://www.moonlight21.com)
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * @link http://www.moonlight21.com/class-XMLHttpRequest-php Comments & suggestions
 * @link http://www.moonlight21.com/class-XMLHttpRequest-php Available at
 */

/*
class XMLHttpRequest
{
	// Properties
	readonly resource curl;
	readonly string error;
	int maxRedirects;
	readonly int readyState;
	readonly string responseText;
	readonly object responseXML;
	readonly int status;
	readonly string statusText;

	// Methods
	string getAllResponseHeaders();
	string getResponseHeader(string $label);
	void open(string $method, string $url[, boolean $async[, string $user[, string $password]]]);
	bool opt(int $option, mixed $value);
	void send([string $data]);
	void setRequestHeader(string $label, string $value);
}
*/

/**
 * @property-read resource $curl Gets the cURL handle.
 * @property-read string $error Gets the last error message for the last cURL operation.
 * @property int $maxRedirects Gets or sets the maximum amount of HTTP redirections to follow (default:0).
 * @property-read int $readyState Gets the current state of the request operation.
 * @property-read string $responseText String version of data returned from server process.
 * @property-read object $responseXML DOM-compatible document object of data returned from server process.
 * @property-read int $status Gets the http status code returned by server as a number (e.g. 404 for "Not Found" or 200 for "OK").
 * @property-read string $statusText Gets the http status code returned by server as a string (e.g. "Not Found" or "OK").
 */
class XMLHttpRequest
{
/**
 *	@access private
 */
	private $curl;
	private $responseHeaders;
	private $headers;
	private $is_safe_mode = true;
	private $properties = array();

	/**
	 *	@access private
	 */
	public function __set($property, $value)
	{
		$property = strtolower($property);

		switch ($property)
		{
			case "maxredirects":
				$this->properties["maxredirects"] = ((int)$value);
				break;
			case "curl":
			case "error":
			case "readystate":
			case "responsetext":
			case "responsexml":
			case "status":
			case "statustext":
				throw new Exception("property \"$property\" cannot be assigned to -- it is read only");
				break;
			default:
				throw new Exception("class \"".__CLASS__."\" does not contain a definition for \"$property\"");
		}
	}

	/**
	 *	@access private
	 */
	public function __get($property)
	{
		$property = strtolower($property);

		switch ($property)
		{
			case "curl":
				return $this->curl;
			case "error":
				return curl_error($this->curl);
			case "maxredirects":
			case "readystate":
			case "responsetext":
			case "status":
			case "statustext":
				if(isset($this->properties[$property]))
				{
					return $this->properties[$property];
				}else return NULL;
			case "responsexml":
				if(!isset($this->properties["responsexml"]))
				{
					if(isset($this->properties["responsetext"]) && !empty($this->properties["responsetext"]))
					{
						$xml = DOMDocument::loadXML($this->properties["responsetext"], LIBXML_ERR_NONE | LIBXML_NOERROR);
						if($xml)
						{
							$this->properties["responsexml"] = $xml;
							return $xml;
						}
					}
				}else
				{
					return $this->properties["responsexml"];
				}
				return null;
			default:
				throw new Exception("class \"".__CLASS__."\" does not contain a definition for \"$property\"");
		}
	}

	/**
	 * Class constructor.
	 * Initializes a new instance of the class.
	 */
	public function __construct()
	{
		if(function_exists("curl_init"))
		{
			$this->is_safe_mode = ((boolean)@ini_get("safe_mode") === FALSE) ? FALSE : TRUE;
			$this->curl = curl_init();

			if(isset($_SERVER['HTTP_USER_AGENT']))
			{
				$this->opt(CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
			}
			else
			{
				$this->opt(CURLOPT_USERAGENT, "XMLHttpRequest/1.0");
			}

			$this->opt(CURLOPT_HEADER, true);
			$this->opt(CURLOPT_AUTOREFERER, true);
			$this->opt(CURLOPT_RETURNTRANSFER, true);
			$this->opt(CURLOPT_ENCODING, "gzip,deflate");
		}
		else
		{
			throw new Exception("Could not initialize cURL library");
		}
	}

	/**
	 * Class destructor.
	 * Closes the cURL session and frees all resources.
	 */
	public function __destruct()
	{
		curl_close($this->curl);
	}

	/**
	 *	Returns a String that represents the current Object.
	 *	@return string A String that represents the current Object.
	 */
	public function __toString()
	{
		return __CLASS__;
	}

	/**
	 * Set an option for a cURL transfer
	 * @link http://www.php.net/manual/function.curl-setopt.php
	 * @param int $option The CURLOPT_XXX option to set.
	 * @param mixed $value The value to be set on option .
	 * @return bool
	 */
	public function opt($option, $value)
	{
		return curl_setopt($this->curl, $option, $value);
	}

	/**
	 *	Specifies the method, URL, and other optional attributes of a request.
	 *	@link http://www.w3.org/TR/XMLHttpRequest/#dfn-open
	 *	@param String $method HTTP Methods defined in section 5.1.1 of RFC 2616 http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
	 *	@param String $url Specifies either the absolute or a relative URL of the data on the Web service.
	 *	@param Bolean $async FakeSauro Erectus.
	 *	@param String $user specifies the name of the user for HTTP authentication.
	 *	@param String $password specifies the password of the user for HTTP authentication.
	 *	@return void
	 */
	public function open($method, $url, $async = false, $user = "", $password = "")
	{
		$this->properties = array("readystate" => 0);
		$this->responseHeaders;
		$this->headers = array();

		if(!empty($method) && !empty($url))
		{
			$method = strtoupper(trim($method));

			if(!preg_match("/^(GET|POST|HEAD|TRACE|PUT|DELETE|OPTIONS)$/", $method))
			{
				throw new Exception("Unknown HTTP method \"$method\"");
			}

			$referer = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);

			if(!empty($referer) )
			{
				$this->opt(CURLOPT_REFERER, $referer);
			}
			elseif(isset($_SERVER['HTTP_REFERER']))
			{
				$this->opt(CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
			}

			$this->opt(CURLOPT_URL, $url);

			if($method == "POST")
			{
				$this->opt(CURLOPT_POST, 1);
			}
			elseif($method == "GET")
			{
				$this->opt(CURLOPT_POST, 0);
			}
			else
			{
				$this->opt(CURLOPT_POST, 0);
				$this->opt(CURLOPT_CUSTOMREQUEST, $method);
			}

			if(preg_match("/^(https)/", $url))
			{
				$this->opt(CURLOPT_SSL_VERIFYPEER, false);
			}


			if(!empty($user))
			{
				$this->opt(CURLOPT_HTTPAUTH, CURLAUTH_ANY);
				$this->opt(CURLOPT_USERPWD, $user.":". $password);
			}
		}
	}

	/**
	 *	Assigns a label/value pair to the header to be sent with a request.
	 *	@link http://www.w3.org/TR/XMLHttpRequest/#dfn-setrequestheader
	 *	@param String $label Specifies the header label.
	 *	@param String $value Specifies the header value.
	 *	@return void
	 */
	public function setRequestHeader($label, $value)
	{
		$this->headers[] = "$label: $value";
		$this->opt(CURLOPT_HTTPHEADER, $this->headers);
	}

	/**
	 *	Returns complete set of headers (labels and values) as a string.
	 *	@link http://www.w3.org/TR/XMLHttpRequest/#dfn-getallresponseheaders
	 *	@return string Complete set of headers (labels and values) as a string
	 */
	public function getAllResponseHeaders()
	{
		return $this->responseHeaders;
	}

	/**
	 *	Returns the value of the specified http header.
	 *	@link http://www.w3.org/TR/XMLHttpRequest/#dfn-getresponseheader.
	 *	@param String $label
	 *	@return String|null The string value of a single header label.
	 */
	public function getResponseHeader($label)
	{
		$value = array();
		preg_match_all('/^(?s)'.$label.': (.*?)\r\n/im', $this->responseHeaders, $value);//preg_match_all('/(?s)'.$label.': (.*?)\r\n/i', $this->responseHeaders, $value);

		if(count($value ) > 0)
		{
			return implode(', ', $value[1]);
		}
		return null;
	}

	/**
	 *	Transmits the request, optionally with postable string or DOM object data.
	 *	@link http://www.w3.org/TR/XMLHttpRequest/#dfn-getresponseheader
	 *	@param String $data
	 *	@return void
	 */
	public function send($data = NULL)
	{		$this->opt(CURLOPT_FOLLOWLOCATION, true);
		if($data !== NULL)
		{
			$this->opt(CURLOPT_POSTFIELDS, $data);
			$this->opt(CURLOPT_FOLLOWLOCATION, true);

		}

		if(isset($this->properties["maxredirects"]) && $this->properties["maxredirects"] && !$this->is_safe_mode)
		{
			$this->opt(CURLOPT_MAXREDIRS, $this->properties["maxredirects"]);
			$this->opt(CURLOPT_FOLLOWLOCATION, true);
		}

		$response = curl_exec($this->curl);
		$header_size = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
		$raw_header  = substr($response, 0, $header_size - 4);
		$headerArray = explode("\r\n\r\n", $raw_header);
		$header = $headerArray[count($headerArray) - 1];

		/**
		 * workaround PHP safe_mode
		 */
		if(isset($this->properties["maxredirects"]) && $this->properties["maxredirects"] && $this->is_safe_mode)
		{
			$location = array();
			$count = 0;

			while(preg_match('/Location:(.*?)\s\n/im', $header, $location) && ( $count <= $this->properties["maxredirects"]) )
			{
				$count++;
				$referer = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
				$location = parse_url(trim(array_pop($location)));
				$last_url = parse_url($referer);

				if (!isset($location['scheme']))$location['scheme'] = $last_url['scheme'];
				if (!isset($location['host']))$location['host'] = $last_url['host'];
				if (!isset($location['path']))$location['path'] = $last_url['path'];

				$next_url = $location['scheme'] . '://' . $location['host'] . $location['path'] . (isset($location['query'])?'?'.$location['query']:'');

				$this->opt(CURLOPT_POST, 0);
				$this->opt(CURLOPT_REFERER, $referer);
				$this->opt(CURLOPT_URL, $next_url);

				$response = curl_exec($this->curl);
				$header_size = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
				$raw_header  = substr($response, 0, $header_size - 4);
				$headerArray = explode("\r\n\r\n", $raw_header);
				$header = $headerArray[count($headerArray) - 1];
			}
		}

		$this->properties["responsetext"] = substr($response, $header_size);

		$sT = array();
		preg_match('/^HTTP\/\d\.\d\s+(\d{3}) (.*)\s\n/i',$header ,$sT);

		if(count($sT ) > 2)
		{
			$this->responseHeaders = str_ireplace($sT[0],"",$header)."\r\n\r\n";
			$this->properties["status"] = $sT[1];
			$this->properties["statustext"] = $sT[2];
		}
		$this->properties["readystate"] = 4;
	}
}
?>