<?php 
namespace Models\Session;

use System\Base\Model;

class Index extends Model {
    protected static $instance;
    protected string $pk = "id_session";
    protected string $table = "sessions";

    public function getByToken(string $token) : ?array
    {
        $result = $this->selector()->where('token=:token', ['token' => $token])->get(); 
        return $result[0] ?? null;
    }
}