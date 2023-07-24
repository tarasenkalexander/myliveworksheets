<?php
namespace Models\Users;

use System\Base\Model;

class Index extends Model
{
    protected static $instance;
    protected string $pk = "id_user";
    protected string $table = "users";

    public function getByEmail(string $email): ?array
    {
        $result = $this->selector()->where('email=:email', ['email' => $email])->get();
        return $result[0] ?? null;
    }
}