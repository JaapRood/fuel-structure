# DEPRECATED! Changed to [fuel-representation](https://github.com/JaapRood/fuel-representation), as that makes more sense!

#Structure
### Structure is a FuelPHP package to manage your RESTful data representations

* [By Jaap Rood](http://www.jaaprood.nl)

## Why you'd need it

I found myself needing this in projects where I use Fuel's REST controller. In order to add links to my resources (e.g. with a Post resource, the link to the author), I need to able to define the outgoing data properly. In the case of a User resource, you would like to remove the sensitive info like passwords.

To keep the representations of my resources consistent and managable, I created this class which takes data and restructures it. The restructuring takes place in templates you can predefine. It's basically a mix between the way View and Config files are used

## Usage

```php
$example_data = array('cool','stuff','happens');

$structured_data = Structure::factory($example_data)->to('templatefile');
```

To structure data, we need to know how this should look. In order to define this, template files are used similarly to views. You input the data into a template that uses it to create the output you'd like. However, just like in config files, you return the data in the template file.

These template files can contain any logic you'd like. You can choose to completely define your datastructure, or to just edit the one you have.


### Example: editing a user for use in an API

The call

```php
$user = array( // this could be anything, like a Model
  'id'        => 3,
  'name'      => 'Jaap Rood',
  'website'   => 'http://jaaprood.nl'
  'password'  => '40eac98fb9843982ea98b9caa'
);

$structured_data = Structure::factory(array('user' => $user))->to('api/user');
```

The template

```php
unset($user['password']);
$user['link'] = Uri::create('api/users/'. $user['id']);

return $user;
```

If you really start using this for alot of representations, making the output more explicit might be a good idea 

```php
// this way you always have control over the data you output
return array(
  'id'        => $user['id'],
  'name'      => $user['name'],
  'website'   => 'http://jaaprood.nl'
  'link'      => Uri::create('api/users/'. $user['id']);
);
```

### Models to array

From working with a couple of Javascript Frameworks, I know that when using the normal Model->to_array() method creates associative arrays. These, however, will not create native JSON arrays but instead objects with numerical keys. Many Javascript frameworks don't play nice with this.

In order to address this problem, and because I basically do it in this part of my Apps, I included a helper function to convert Models without using associative arrays (recursively).

```php
$user = Model_User::find('all');

Structure::models_to_array($user);
```

## Helping out

Please let me know about bugs, problems, issues, ideas, cool features, awesome places to get pasta, etc!