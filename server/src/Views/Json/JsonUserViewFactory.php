<?php

namespace Cora\Views;

class JsonUserViewFactory implements UserViewFactoryInterface {
    public function createUserView(): UserViewInterface {
        return new JsonUserView();
    }

    public function createUsersView(): UsersViewInterface {
        return new JsonUsersView();
    }

    public function createUserCreatedView(): UserCreatedViewInterface {
        return new JsonUserCreatedView();
    }
}
