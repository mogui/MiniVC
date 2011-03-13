#MiniVC

MiniVC is a simple, one-file PHP 5 Framework, it implements a basic [MVC design pattern](http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller).

##the M
More specifically it miss the M (Model) allowing everyone to use whatever DB wrapper or ORM one likes, (personally I often use [AdoDB Active record](http://phplens.com/lens/adodb/docs-active-record.htm)).
I intentionally missed a personal Model implementation because this is a really basic framework to quick realize small application which focuses on business logic and of small dimension, and cause PHP is full of fantastic DB wrapper and ORM library.

##The C
The controller is a basic Controller as a lot of other Framework, it has some facility method to do GET/POST calls with cURL, it could be  more complex but the idea is to keep things small.   
All controller must be named _name_Controller.php.  
All the framework expects to have a working mod_rewrite.  
The urls are parsed basically with this pattern **/controller_name/action/param1/param2** which will instantiate a controller named **controller_name** and fire a method named **action** with **param1** and **param2** as parameters in the call.

	$contr = new controller_name();  
	$contr->action("parm1","param2");

to override this default behaviour we can setup a static variable in the MiniVC class named $urls that is an assoc array that maps url regex (ex. "^/add_film/([0-9]+/$)") to another url string that represent a valid call as described above.   
This is inspired by the way [Django (a Python Framework)](http://www.djangoproject.com/) manages urls and firing actions.

##The V
The managing of view is also simple and basic. From the controller after have done with our business logic we can (or not if we wanto to output json or xml for example) call a method to render a layout (the V) passing it an array of vars that will be available from the layout file.   
There's a built in hierarchical layout system, also inspiresd by Django framework. It lets use hereditary in templates and lets include html fragment in other layout.   

##Filesystem layout
The Framework defaults to this filesystem structure and i suggest to use this:

	rootDirectory/  
		public/   
			.htaccess		//forward to index.php all request write it as you like   
			index.php 		//includes the framework and init it (a sort of bootstrap)   
			css/   
			js/   
			img/   
		classes/   
			yourController.php   
			anotherController.php    
		layout/   
			your_template.php   
			another_template   

Any way all can be set differently

##Conclusion
For a use guide just see the basic project it is very simple and documented.   
Every help is a lot appreciated!