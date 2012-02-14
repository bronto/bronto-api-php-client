# Bronto_Api

ActiveRecord style abstraction of the Bronto SOAP API.

## Implemented Objects

 * Activity
 * Contact
 * Conversion
 * Delivery
 * Deliverygroup
 * Field
 * List
 * Message
 * Messagerule

## Example Code

### Login

```php
/* @var $bronto \Bronto_Api */
$bronto = new \Bronto_Api();
$bronto->setToken($token); // Or pass $token to the constructor of Bronto_Api
$bronto->login(); // Only needs to be called once
```

### Create new Contact

```php
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

### Delete a Contact

```php
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

```php
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

### Read List by Name

```php
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

### Create new Field

```php
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

### Retrieve a Message

```php
/* @var $messageObject \Bronto_Api_Message */
$messageObject  = $bronto->getMessageObject();

/* @var $message \Bronto_Api_Message_Row */
$message = $messageObject->createRow(array('id' => '123'));
$message->read();
```

### Create a Delivery

```php
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
