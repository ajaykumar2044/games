<?php
/**
 *
 * @author learp
 */

use Illuminate\Support\Collection;
use App\Token;

class UserWithTokenRelationTest extends TestCase
{
    /**
     *
     * @var $token
     */
    protected $token;
    
    public function setUp()
    {
        parent::setUp();
        $this->token = factory(Token::class)->create();
    }
    
    public function testUserToTokenIsCollection()
    {
        $token = $this->token;
        $user = $token->user;
        $this->assertTrue($user->tokens instanceof Collection);
    }
    
    /**
     *
     * @depends testUserToTokenIsCollection
     */
    public function testUserToTokenRelation()
    {
        $token = $this->token;
        $user = $token->user;
        $this->assertTrue($user->tokens->contains($token));
    }
    
    public function testTokenToUserRelation()
    {
        $this->assertEquals($this->token->user->id, $this->token->user_id);
    }
    
    public function tearDown()
    {
        $token = $this->token;
        /*
         * Delete related autogenerated models also
         */
        $token->user->gameType->delete();
        $token->user->delete();
        $token->delete();
        parent::tearDown();
    }
}
