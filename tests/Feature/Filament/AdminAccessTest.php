<?php

use App\Models\User;

beforeEach(function () {
    config()->set('portfolio.admin_emails', ['owner@example.com']);
});

it('redirects guests to the admin login page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('forbids signed-in users who are not allowlisted', function () {
    $this->actingAs(User::factory()->create(['email' => 'visitor@example.com']))
        ->get('/admin')
        ->assertForbidden();
});

it('lets allowlisted users into the admin panel', function () {
    $this->actingAs(User::factory()->create(['email' => 'owner@example.com']))
        ->get('/admin')
        ->assertOk();
});
