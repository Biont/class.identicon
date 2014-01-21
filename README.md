# EXCERPT from Wikipedia

An Identicon is a visual representation of a hash value, usually of the
IP address, serving to identify a user of a computer system. The
original Identicon is a 9-block graphic, which has been extended to
other graphic forms by third parties some of whom have used MD5 instead
of the IP address as the identifier. In summary, an Identicon is a
privacy protecting derivative of each user's IP address built into a
9-block image and displayed next the user's name. A visual
representation is thought to be easier to compare than one which uses
only numbers and more importantly, it maintains the person's privacy.
The Identicon graphic is unique since it's based on the users IP, but
it is not possible to recover the IP by looking at the Identicon.

## About this project




## ABOUT PHP-Identicons


The original code was taken from here:

[http://sourceforge.net/projects/identicons/](http://sourceforge.net/projects/identicons/)

PHP-Identicons is a lightweight PHP implementation of Don Park's
original identicon code for visual representation of MD5 hash values.
The program uses the PHP GD library for image processing.

The code can be used to generate unique identicons, avatars, and
system-assigned images based on a user's e-mail address, user ID, etc.


## INSTALLATION

Include the class.identicon.php file in your project


## USAGE

The basic usage of this class is like this:

```

$identicon = new Identicon('',256);
echo $identicon->image();

```

which will output something an image tag like this:

```

<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAQAAAAEACAIAAADTE (...) vn/wEcisrTmERAjwAAAABJRU5ErkJggg==">

```


Have a look at the index.html

## TODO

* Implement a good way to be able to save the images on the server. Right now you can only output the base64-encoded image data

