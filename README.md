# Bronto_Api

**This project is no longer maintained and should be used for historical purposes only.**

Client Library for PHP - ActiveRecord Style Abstraction of the Bronto SOAP API.

This library was created and is maintained by the Bronto Professional Services Engineering team.<br/>
It is free to use, but comes with no official support.

## Implemented Objects

 * Account
 * Activity
 * ApiToken
 * Contact
 * ContentTag
 * Conversion
 * Delivery
 * Deliverygroup
 * Field
 * List
 * Login
 * Message
 * Messagerule
 * Segment

## Example Code

### Login

```php
<?php

/* @var $bronto \Bronto_Api */
$bronto = new \Bronto_Api();
$bronto->setToken($token); // Or pass $token to the constructor of Bronto_Api
$bronto->login(); // Only needs to be called once
```

### Create new Contact

```php
<?php

/* @var $contactObject \Bronto_Api_Contact */
$contactObject = $bronto->getContactObject();

/* @var $contact \Bronto_Api_Contact_Row */
$contact = $contactObject->createRow();
$contact->email  = 'user@example.com';
$contact->status = \Bronto_Api_Contact::STATUS_ONBOARDING;

// Add Contact to List
$contact->addToList($list); // $list can be the (string) ID or a Bronto_Api_List instance

// Set a custom Field value
$contact->setField($field, $value); // $field can be the (string) ID or a Bronto_Api_Field instance

// Save
try {
    $contact->save();
} catch (Exception $e) {
    // Handle error
}
```

### Create/Update many Contacts

```php
<?php

/* @var $contactObject \Bronto_Api_Contact */
$contactObject = $bronto->getContactObject();

/* @var $contacts \Bronto_Api_Rowset */
$contacts = $contactObject->addOrUpdate(array(
    array('email' => 'joe.doe+1@example'),
    array('email' => 'joe.doe+2@example'),
    array('email' => 'joe.doe+3@example'),
));

```

### Delete a Contact

```php
<?php

/* @var $contactObject \Bronto_Api_Contact */
$contactObject = $bronto->getContactObject();

try {
    /* @var $contact \Bronto_Api_Contact_Row */
    $contact->delete();
} catch (Exception $e) {
    // Handle error
}
```

### Read Contacts using Filter

##### Option #1: Using paging

```php
<?php

/* @var $contactObject \Bronto_Api_Contact */
$contactObject = $bronto->getContactObject();

// Filter by status
$contactsFilter['status'] = array(\Bronto_Api_Contact::STATUS_TRANSACTIONAL);

// ... and by created after date
$contactsFilter['created'] = array(
    'operator' => 'After',
    'value'    => date('c', time() - (86400 * 7)),
);

// ... and is on a list
$contactsFilter['listId'] = array($list->id);

$contactsCounter = 0;
$contactsPage    = 1;
while ($contacts = $contactObject->readAll($contactsFilter, array(), false, $contactsPage)) {
    if (!$contacts->count()) {
        break;
    }

    foreach ($contacts as $contact /* @var $contact \Bronto_Api_Contact_Row */) {
        echo "{$contactsCounter}. {$contact->email}\n";
        $contactsCounter++;
    }

    $contactsPage++;
}
```

##### Option #2: Using iterator

```php
<?php

// ...

$contactsCounter = 0;
foreach ($contactObject->readAll($contactsFilter)->iterate() as $contact /* @var $contact \Bronto_Api_Contact_Row */) {
    echo "{$contactsCounter}. {$contact->email}\n";
    $contactsCounter++;
}
```

### Read List by Name

```php
<?php

/* @var $listObject \Bronto_Api_List */
$listObject = $bronto->getListObject();

/* @var $list \Bronto_Api_List_Row */
$list = $listObject->createRow();
$list->name = 'My Example List';
try {
    $list = $list->read();
} catch (Exception $e) {
    // Handle error
}
```

### Clear List(s)
```php
<?php

/* @var $listObject \Bronto_Api_List */
$listObject = $bronto->getListObject();

/ @var $listIds \Array */
$listIds = array(array(id => '0bbd03ec000000000000000000000003c2a1'));
try {
    $response = $listObject->clear($listIds);
  
    // Check for errors
    if ($response->hasErrors()) {
        $error = $response->getError();
        throw new Exception($error['message']);
    }
} catch (Exception $e) {
    // Handle error
}
```

### Create new Field

```php
<?php

/* @var $fieldObject \Bronto_Api_Field */
$fieldObject = $bronto->getFieldObject();

/* @var $field \Bronto_Api_Field_Row */
$field = $fieldObject->createRow();
$field->name = $name;
try {
    $field->save();
} catch (Exception $e) {
    // Handle error
}
```

### Retrieve a ContentTag

```php
<?php

/* @var $contentTagObject \Bronto_Api_ContentTag */
$contentTagObject  = $bronto->getContentTagObject();

/* @var $contentTag \Bronto_Api_ContentTag_Row */
$contentTag = $contentTagObject->createRow();
$contentTag->id = '123';
try {
    $contentTag = $contentTag->read();
} catch (Exception $e) {
    // Handle error
}
```

### Retrieve a Message

```php
<?php

/* @var $messageObject \Bronto_Api_Message */
$messageObject  = $bronto->getMessageObject();

/* @var $message \Bronto_Api_Message_Row */
$message = $messageObject->createRow(array('id' => '123'));
$message->read();
```

### Create a Delivery

```php
<?php

/* @var $deliveryObject \Bronto_Api_Delivery */
$deliveryObject = $bronto->getDeliveryObject();

/* @var $delivery \Bronto_Api_Delivery_Row */
$delivery = $deliveryObject->createRow();
$delivery->start      = date('c'); // Today
$delivery->type       = \Bronto_Api_Delivery_Row::TYPE_TRANSACTIONAL;
$delivery->messageId  = $message->id;
$delivery->fromEmail  = 'user@example.com';
$delivery->fromName   = 'Example Sender';
$delivery->recipients = array(
    array(
        'type' => 'contact',
        'id'   => $contact->id,
    ),
);
$delivery->save();
```

### Read a Delivery

```php
<?php

/* @var $deliveryObject \Bronto_Api_Delivery */
$deliveryObject = $bronto->getDeliveryObject();

/* @var $delivery \Bronto_Api_Delivery_Row */
$delivery = $deliveryObject->createRow(array(
    'id' => 'some delivery id'
));
$delivery->read();
```

### Read Recipients from a Delivery

```php
<?php

/* @var $delivery \Bronto_Api_Delivery_Row */
$recipients = $delivery->getRecipients();

foreach ($recipients as $recipient /* @var $recipient \Bronto_Api_Delivery_Recipient */) {
    // Do something with $recipient
}
```
