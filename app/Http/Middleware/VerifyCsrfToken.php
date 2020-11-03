<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'delete/*',
        'update',
        'store',
        'addcategory',
        'additem',
        'upload',
        'storerequest',
        'remove',
        'service-out',
        'service-in',
        'pull-out',
        'rep-update',
        'loan',
        'loansapproved',
        'loanstock',
        'loanupdate',
        'return-update',
        'storerreceived',
        'loandelete',
        'branch_ini',
        'branch_add',

    ];
}
