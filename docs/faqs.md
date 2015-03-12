# FAQs #

## 1) ACL session troubleshooting ##

ACL permissions are stored in the session upon logging in. Sometimes this process fails and action buttons (create, delete, etc) are not present. This can also happen when modifying ACL records that will require a re-login. To solve this problem, the session must be destroyed upon logout and the user will need to re-login to the admin panel.

```php
public function logout() {
    $this->Session->destroy();
    $this->redirect($this->Auth->logout());
}
```

## 2) Why are the admin pages publicly available? ##

This is usually caused by a `$this->Auth->allow()` in the `AppController` or another inheriting controller. All pages should never be publicly allowed by default.
