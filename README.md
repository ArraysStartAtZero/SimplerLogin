# SimplerLogin Redirect
Wordpress simple log in plugin that redirects after logging in. 

Add the text from php file to the functions.php and than you should be able to see options in "settings" tab in the administration panel. 

Change this section(in the php.file) according to what roles and what redirects you need. 
    // Define role-based redirects
    $role_redirects = array(
        'administrator' => '/admin-dashboard/',
        'user' => '/user/',
        'user2' => '/user2/',
    );


After adding that code it should work, tested on latest wp. 
