<?php

namespace Cora\Views;

interface UserViewFactoryInterface {
    public function createUserView(): UserViewInterface;
    public function createUsersView(): UsersViewInterface;
    public function createUserCreatedView(): UserCreatedViewInterface;
}
