<?php
namespace Venu\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test class for Friend controller
 */
class FriendControllerTest extends WebTestCase
{

    /**
     * Client
     * @var type
     */
    private $client;

    /**
     * Service Container  fos_user.user_manager
     * @var type
     */
    private $userManager;

    /**
     * Authentication header
     * @var type
     */
    private $header;

    /**
     * Test environment setup
     *
     * @return none
     */
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->userManager = $kernel->getContainer()->get('fos_user.user_manager');
       
        $this->userManager = $kernel->getContainer()->get('fos_user.user_manager');
        $this->user1 = $this->userManager->findUserByUsernameOrEmail("venu1@riktamtech.com");
        if (!$this->user1) {
            $this->user1 = $this->userManager->createUser();
            $this->user1->setEmail('venu1@riktamtech.com');
            $this->user1->setPlainPassword('12345');
            $this->user1->setEnabled(true);
            $this->user1->addRole('ROLE_DEVELOPER');
            $this->userManager->updateUser($this->user1);
        }

        $this->user2 = $this->userManager->findUserByUsernameOrEmail("venu2@riktamtech.com");
        if (!$this->user2) {
            $this->user2 = $this->userManager->createUser();
            $this->user2->setEmail('venu2@riktamtech.com');
            $this->user2->setPlainPassword('12345');
            $this->user2->setEnabled(true);
            $this->user2->addRole('ROLE_DEVELOPER');
            $this->userManager->updateUser($this->user2);
        } else {
            $this->user2->setPlainPassword('12345');
            $this->userManager->updateUser($this->user2);
        }
        
        $this->user3 = $this->userManager->findUserByUsernameOrEmail("venu3@riktamtech.com");
        if (!$this->user3) {
            $this->user3 = $this->userManager->createUser();
            $this->user3->setEmail('venu3@riktamtech.com');
            $this->user3->setPlainPassword('12345');
            $this->user3->setEnabled(true);
            $this->user3->addRole('ROLE_DEVELOPER');
            $this->userManager->updateUser($this->user3);
        } else {
            $this->user3->setPlainPassword('12345');
            $this->userManager->updateUser($this->user3);
        }
        
        /*
         * Creation of the client with the admin authenticated header
         */
        $user = $this->userManager->findUserByUsernameOrEmail("venu1@riktamtech.com");
        if ($user) {
            $username = $user->getUsername();
            $password = $user->getPassword();
            $created = date('c');
            $nonce = substr(md5(uniqid('nonce_', true)), 0, 16);
            $nonceSixtyFour = base64_encode($nonce);
            $passwordDigest = base64_encode(sha1($nonce . $created . $password, true));
            $token = "UsernameToken Username=\"{$username}\", PasswordDigest=\"{$passwordDigest}\", Nonce=\"{$nonceSixtyFour}\", Created=\"{$created}\"";
            $this->header = array(
                'HTTP_Authorization' => 'WSSE profile="UsernameToken"',
                'HTTP_X-WSSE' => $token,
                'HTTP_ACCEPT' => 'application/json'
            );
            $this->client = static::createClient(array(), $this->header);
        }
    }
    

    /**
     * Test Add Friend
     *
     * @return none
     */
    public function testAddFriend()
    {
        $this->client->request('POST', '/api/users/' . $this->user1->getId() . '/friends/' . $this->user2->getId(), array(), array(), $this->header);

        // Assert a specific 200 status code
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());

        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        
        $this->client->request('POST', '/api/users/' . $this->user1->getId() . '/friends/' . $this->user3->getId(), array(), array(), $this->header);

        // Assert a specific 200 status code
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());

        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }
    
     
    
    /**
     * Test Add Friend
     *
     * @return none
     */
    public function testIgnoreFriend()
    {
        $username = $this->user3->getUsername();
        $password = $this->user3->getPassword();
        $created = date('c');
        $nonce = substr(md5(uniqid('nonce_', true)), 0, 16);
        $nonceSixtyFour = base64_encode($nonce);
        $passwordDigest = base64_encode(sha1($nonce . $created . $password, true));
        $token = "UsernameToken Username=\"{$username}\", PasswordDigest=\"{$passwordDigest}\", Nonce=\"{$nonceSixtyFour}\", Created=\"{$created}\"";
        $header = array(
            'HTTP_Authorization' => 'WSSE profile="UsernameToken"',
            'HTTP_X-WSSE' => $token,
            'HTTP_ACCEPT' => 'application/json'
        );
        $client = static::createClient(array(), $header);
        
        $client->request('PATCH', '/api/users/' . $this->user3->getId() . '/friends/' . $this->user1->getId() .'/ignore', array(), array(), $header);
        //echo $content = $client->getResponse()->getContent();
        

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }
    
    /**
     * Test Add Friend
     *
     * @return none
     */
    public function testAcceptFriend()
    {
        $username = $this->user2->getUsername();
        $password = $this->user2->getPassword();
        $created = date('c');
        $nonce = substr(md5(uniqid('nonce_', true)), 0, 16);
        $nonceSixtyFour = base64_encode($nonce);
        $passwordDigest = base64_encode(sha1($nonce . $created . $password, true));
        $token = "UsernameToken Username=\"{$username}\", PasswordDigest=\"{$passwordDigest}\", Nonce=\"{$nonceSixtyFour}\", Created=\"{$created}\"";
        $header = array(
            'HTTP_Authorization' => 'WSSE profile="UsernameToken"',
            'HTTP_X-WSSE' => $token,
            'HTTP_ACCEPT' => 'application/json'
        );
        $client = static::createClient(array(), $header);
        
        $client->request('PATCH', '/api/users/' . $this->user2->getId() . '/friends/' . $this->user1->getId() .'/accept', array(), array(), $header);
       // echo $content = $client->getResponse()->getContent();
        

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }
    
    /**
     * Test Add Friend
     *
     * @return none
     */
    public function testdeleteFriend()
    {
        $this->client->request('DELETE', '/api/users/' . $this->user1->getId() . '/friends/' . $this->user2->getId(), array(), array(), $this->header);
        echo $content = $this->client->getResponse()->getContent();
        

        // Assert a specific 200 status code
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }
    
    /**
     * Test Add Friend
     *
     * @return none
     */
    public function testGetFriends()
    {
        $this->client->request('GET', '/api/users/' . $this->user1->getId() . '/friends', array(), array(), $this->header);
       echo $content = $this->client->getResponse()->getContent();
        

        // Assert a specific 200 status code
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }

   
}
