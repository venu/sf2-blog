<?php
namespace Venu\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test class for user rest controller
 */
class UserRestControllerTest extends WebTestCase
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

        $user3 = $this->userManager->findUserByUsernameOrEmail("venu3@riktamtech.com");
        if ($user3) {
            $this->userManager->deleteUser($user3);
        }


        /*
         * Creation of the client with the admin authenticated header
         */
        $user = $this->userManager->findUserByUsernameOrEmail("venu@riktamtech.com");
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
     * Test get user
     *
     * @return none
     */
    public function testGetUserAction_valid_user()
    {
        $this->client->request('GET', '/api/users/' . $this->user1->getId(), array(), array(), $this->header);
        $content = $this->client->getResponse()->getContent();
        $user1 = json_decode($content, false);

        // Assert a specific 200 status code
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $this->assertEquals('venu1@riktamtech.com', $user1->email);
    }

    /**
     * Test get user nonexistent slug
     *
     * @return none
     */
    public function testGetUserAction_invalid_user()
    {
        $this->client->request('GET', '/api/users/1');

        // Assert a specific 404 status code
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test get registered users
     *
     * @return none
     */
    public function testGetUserAction_get_registered_users()
    {

        $this->client->request('GET', '/api/users');
        $content = $this->client->getResponse()->getContent();
        $contenidoDecodificado = json_decode($content, false);

        // Assert a specific 200 status code
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertCount(3, $contenidoDecodificado);

        $user1 = $contenidoDecodificado[0];
        $this->assertEquals('venu@riktamtech.com', $user1->email);

        $user2 = $contenidoDecodificado[1];
        $this->assertEquals('venu1@riktamtech.com', $user2->email);

        $user3 = $contenidoDecodificado[2];
        $this->assertEquals('venu2@riktamtech.com', $user3->email);
    }

    /**
     * Test create user
     *
     * @return none
     */
    public function testPostUsersAction()
    {
        $params = array('name' => 'venu3',
            'plainPassword' => '12345',
            'email' => 'venu3@riktamtech.com');

        $this->client->request('POST', '/api/users', $params, array());
    
        // Assert a specific 200 status code
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());

        //limpiar usuario creado
        $user = $this->userManager->findUserByUsernameOrEmail("venu3@riktamtech.com");
        $this->assertNotNull($user);
        if ($user) {
            $this->userManager->deleteUser($user);
        }
    }

    /**
     * Test create user invalid data
     *
     * @return none
     */
    public function testPostUsersAction_invalid_data()
    {
        $jsonParam = "username=u3&plainPassword=1&email=" . urlencode("venu.com");
        //echo $jsonParam;

        $this->client->request('POST', '/api/users', array(), array(), array('CONTENT_TYPE' => 'application/x-www-form-urlencoded'), $jsonParam);

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());

        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));

    }

    /**
     * Test update user
     *
     * @return none
     */
    public function testPutUserAction()
    {
        $params = array('password' => '54321','new_password' => '54321');

        $this->client->request('PUT', '/api/users/' . $this->user2->getId(), $params);

        // Assert a specific 200 status code
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());

    }

    /**
     * Test update user invalid slug
     *
     * @return none
     */
    public function testPutUserAction_invalid_slug()
    {
        $params = array('password' => '54321','new_password' => '54321');

        $this->client->request('PUT', '/api/users/' . 1, $params);

        // Assert a specific 200 status code
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test update user invalid password
     *
     * @return none
     */
    public function testPutUserAction_invalid_password()
    {
        $params = array('password' => 'asd','new_password' => '54321');
        //$headers = array('CONTENT_TYPE' => 'application/x-www-form-urlencoded');

        $this->client->request('PUT', '/api/users/' . $this->user2->getId(), $params);

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test update user invalid username
     *
     * @return none
     */
    public function testPutUserAction_invalid_username()
    {
        $params = array('username' => '5');

        $this->client->request('PUT', '/api/users/' . $this->user2->getId(), $params);

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test update user invalid email
     *
     * @return none
     */
    public function testPutUserAction_invalid_email()
    {
        $params = array('email' => '5');

        $this->client->request('PUT', '/api/users/' . $this->user2->getId(), $params);

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
}
