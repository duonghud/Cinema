<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    public function test_admin_entry_redirects_to_login_when_not_authenticated(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_dashboard_redirects_to_login_when_not_authenticated(): void
    {
        $response = $this->get('/admins/dashboard');

        $response->assertRedirect(route('admin.login'));
    }
}
