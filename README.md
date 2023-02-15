## LTI Implementation Example in Laravel

Learning Tools Interoperability (LTI) is an education technology specification developed by the [IMS Global Learning Consortium](http://www.imsglobal.org/activity/learning-tools-interoperability). It specifies a method for a learning system to invoke and to communicate with external systems.

This project implement LTI Advantage as LTI Tool Provider in laravel using [TAO -  LTI 1.3 PHP Framework](https://oat-sa.github.io/doc-lti1p3/libraries/lib-lti1p3-core/)

This project is tested using moodle as a LTI Platform Consumer and [Official LTI Testing tool](https://lti-ri.imsglobal.org/)
## Installation
- clone this repository by using `git clone https://github.com/abanghendri/laravel-lti-example.git`
- `composer install` to install all dependencies
- run `php artisan migrate`
- run `php artisan passport:keys` ro generate RSA Key pair

Now you can run your application.
