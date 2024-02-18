Symfony AjaxResponser Bundle
==========================

[![Latest Stable Version](http://poser.pugx.org/eltharin/ajaxresponser/v)](https://packagist.org/packages/eltharin/ajaxresponser) 
[![Total Downloads](http://poser.pugx.org/eltharin/ajaxresponser/downloads)](https://packagist.org/packages/eltharin/ajaxresponser) 
[![Latest Unstable Version](http://poser.pugx.org/eltharin/ajaxresponser/v/unstable)](https://packagist.org/packages/eltharin/ajaxresponser) 
[![License](http://poser.pugx.org/eltharin/ajaxresponser/license)](https://packagist.org/packages/eltharin/ajaxresponser)


Installation
------------

* Require the bundle with composer:

``` bash
composer require eltharin/ajaxresponser
```


What is AjaxResponser Bundle?

AjaxResponser is a bundle whitch allow to have routes responding with HTML or Json depending the request.

eg: a delete request in HTML will respond HTML page (or redirect) with a flash message, but the same page call by ajax, will respond a Json with the result and the flash message.


Use It :
---------------------------

Just Add AjaxOrNot Attribute to your route : 

``` php
use Eltharin\AjaxResponserBundle\Annotations\AjaxCallOrNot;

...

#[Route(path: '/test-ajax-responser', name: 'app_test_ajax_responser')]
#[AjaxCallOrNot]
public function test_ajax_responser(): Response
{
    //-- Actions

    $this->addFlash('success', 'all is good');

    return $this->redirectToRoute('app_home');
}
```

If you go to <your-site>/test-ajax-responser you will be redirect to your homepage with a flash message all is good

But if your call the same page in AJAX, with a header X-Requested-With with value XMLHttpRequest,

you got a Json Response :
``` JSON
{
    "statusCode": 302,
    "content": "<!DOCTYPE html>\n<html>\n    <head>\n        <meta charset=\"UTF-8\">\n        <title>Hello HomeController!<\/title>\n        <link rel=\"icon\" href=\"data:image\/svg+xml,<svg xmlns=%22http:\/\/www.w3.org\/2000\/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>\u26ab\ufe0f<\/text><\/svg>\">\n                \n                    <\/head>\n    <body>\n    <fieldset class=\"messageContainer\">\n        <legend>Flash Messages<\/legend>\n\n            <\/fieldset>\n        <style>\n    .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px\/1.5 sans-serif; }\n    .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }\n<\/style>\n\n<div class=\"example-wrapper\">\n    <h1>Hello HomeController! \u2705<\/h1>\n\n    This friendly message is coming from:\n    <ul>\n        <li>Your controller at <code><a href=\"file:\/\/D:\/laragon\/www\/testbundles\/src\/Controller\/HomeController.php#L0\">src\/Controller\/HomeController.php<\/a><\/code><\/li>\n        <li>Your template at <code><a href=\"file:\/\/D:\/laragon\/www\/testbundles\/templates\/home\/index.html.twig#L0\">templates\/home\/index.html.twig<\/a><\/code><\/li>\n    <\/ul>\n<\/div>\n\n    <script>\n        window.addEventListener(\"DOMContentLoaded\", (event) => {\n            Flashes.init('.messageContainer');\n        });\n    <\/script>\n    <\/body>\n<\/html>\n",
    "msgs": {
        "success": [
            "all is good"
       ]
    },
    "redirectUrl": "\/home"
}
```

you get : 
- statusCode : the original HTTP return code here 302 for the redirect
- content : content returned after the redirect
- msgs : flashes messages
- redirectUrl : Url who generated the content

Get the content after redirect can offer you to don't make an other ajax for actualize actual page,

eg: you have a page showing many items, you want to delete one with a delete button, the route make the delete action and redirect you to the item page, so you have the HTML and can replace directly.

You can disable getting this content by set the getRedirectContent param to false : 


``` php
use Eltharin\AjaxResponserBundle\Annotations\AjaxCallOrNot;

...

#[Route(path: '/test-ajax-responser', name: 'app_test_ajax_responser')]
#[AjaxCallOrNot(getRedirectContent: false)]
public function test_ajax_responser(): Response
{
    //-- Actions

    $this->addFlash('success', 'all is good');

    return $this->redirectToRoute('app_home');
}
```
 
now you have the redirect HTML.
