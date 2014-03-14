kohana-admin
============

Simple CRUD module written as Kohana 3.3 module

Quickstart
----------

* Add to your composer.json:

```
{
    ...
    "require": {
        ...
        "kohana/admin": "*@dev"
    },
    "repositories": [
        ...
        {
            "type": "git",
            "url": "git://github.com/noxxan/kohana-admin.git"
        }
    ]
}
```

* Copy modules/admin/config/admin.php into your APPPATH/config directory.

* Add 'admin' => MODPATH.'admin' into Kohana::modules call in your bootstrap.php

* Prepare model (a class) which implements Admin_Model_Scaffolding interface
    and implement needed for admin UI methods (in future releases - it will be done automagically).

For example:

```php
<?php
class Model_News implements Admin_Model_Scaffolding
{
    protected $_table = 'news';
    
    public function getAll($page = 1, $limit = 10, $filters = array())
    {
        $offset = ($page-1) * $limit;
    
        $query = DB::query(
            Database::SELECT,
            'SELECT r.* FROM ' . $this->_table . ' r
            ORDER BY r.id DESC
            LIMIT :limit OFFSET :offset'
        )
            ->bind(':limit', $limit)
            ->bind(':offset', $offset)
            ->execute();
    
        return $query->as_array();
    }
    
    public function countAll($filters = array())
    {
        $query = DB::query(
            Database::SELECT,
            'SELECT COUNT(id) FROM ' . $this->_table
        )
            ->execute();
    
        $count = current($query->as_array());
    
        return (int) $count['count'];
    }
    
    public function getOne($id)
    {
        $query = DB::query(
            Database::SELECT,
            'SELECT r.* FROM ' . $this->_table . ' r
            WHERE r.id = :id
            LIMIT 1'
        )
            ->bind(':id', $id)
            ->execute();
        
        return current($query->as_array());
    }
    
    public function create($data)
    {
        $result = DB::query(Database::INSERT, 'INSERT INTO ' . $this->_table . ' (title, short_content, content) VALUES (:title, :short_content, :content)')
            ->bind(':title', $data['title'])
            ->bind(':short_content', $data['short_content'])
            ->bind(':content', $data['content'])
            ->execute();
        
        return !empty($result) ? (int) $result[0] : 0;
    }
    
    public function save($id, $data)
    {
        return DB::query(
            Database::UPDATE,
            'UPDATE ' . $this->_table . '
            SET title = :title, short_content = :short_content, content = :content
            WHERE id = :id'
        )
            ->bind(':id', $id)
            ->bind(':title', $data['title'])
            ->bind(':short_content', $data['short_content'])
            ->bind(':content', $data['content'])
            ->execute();
    }
    
    public function delete($id)
    {
        return DB::query(
            Database::DELETE,
            'DELETE FROM ' . $this->_table . ' WHERE id = :id'
        )
            ->bind(':id', $id)
            ->execute();
    }
}
```

* Create a module class in APPPATH/classes/Admin/Module/News.php called Admin_Module_News with content:

```php
<?php
class Admin_Module_News extends Admin_Module
{
    protected $_name = 'news';
    protected $_displayName = 'News';
    protected $_fields = array(
        'id' => array('type' => 'primary', 'display_name' => 'ID'),
        'title' => array('type' => 'char', 'display_name' => 'Title', 'required' => true),
        'short_content' => array('type' => 'text', 'display_name' => 'Short content', 'required' => true),
        'content' => array('type' => 'text', 'display_name' => 'Content', 'required' => true),
        'add_date' => array('type' => 'datetime', 'display_name' => 'Add date'),
    );
    protected $_listFields = array('id', 'title', 'short_content', 'add_date');
}
```

* Insert 'news' into APPPATH/config/admin.php:

```php
<?php
return array(
    'auth' => array('username' => 'admin', 'password' => 'admin'),
    'modules' => array('news')
);
```

* Put media/ into your DOCROOT path in admin directory.

* Retrieve BASE_URL/admin/login to see result!

# Changelog

## 0.2.2

### added CSV export of given list, to use that feature you need to add for example:

```
    protected $_listActions = array(
        array('Admin_ListAction_ExportCsv', array('fields' => array('id', 'edition_id', 'state', 'quiz_result', 'answer', 'add_date')))
    );
```

to your subclass of Admin_Module, where you want that option.