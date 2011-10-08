#Structure
## Structure is a FuelPHP package to manage your RESTful data representations

* [By Jaap Rood](http://www.jaaprood.nl)

## Why you'd need it

I found myself needing this in projects where I use Fuel's REST controller. In order to add links to my resources (e.g. with a Post resource, the link to the author), I need to able to define the outgoing data properly. In the case of a User resource, you would like to remove the sensitive info like passwords.

To keep the representations of my resources consistent and managable, I created this class which takes data and restructures it. The restructuring takes place in templates you can predefine. It's basically a mix between the way View and Config files are used