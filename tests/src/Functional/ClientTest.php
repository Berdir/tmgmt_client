<?php


namespace Drupal\Core\Tests\tmgmt_client\Functional;

use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\Tests\BrowserTestBase;
use Drupal\tmgmt_server\Entity\TMGMTServerClient;


/**
 * Class ClientTest.
 *
 * @group tmgmt_client
 */
class ClientTest extends BrowserTestBase    {
  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'tmgmt',
    'tmgmt_content',
    'tmgmt_client',
    'tmgmt_server',
    'tmgmt_local',
    'language',
    'content_translation',
    'tmgmt_language_combination',
    'content_translation',
  ];

  /**
   * @var TMGMTServerClient
   */
  public $remote_client;

  public function setUp() {
    parent::setUp();

    // Add second language 'german' to the site.
    $language = ConfigurableLanguage::createFromLangcode('de');
    $language->save();

    // Create clint on the server side, to be targeted.
    $this->remote_client = TMGMTServerClient::create([
      'name' => 'test client',
      'description' => 'used to test the client',
      'url' => 'translator',
    ]);
    $this->remote_client->setKeys();
    $this->remote_client->save();


  }

  public function testClientSetup() {

    global  $base_url;
    $user = $this->drupalCreateUser(['administer tmgmt']);
    $this->drupalLogin($user);
    $edit = [
      'label' => 'Test Client Provider',
      'description' => 'Used for Testing purposes',
      'settings[remote_url]' => $base_url,
      'settings[client_id]' => $this->remote_client->getClientId(),
      'settings[client_secret]' => $this->remote_client->getClientSecret(),
    ];
    $this->drupalPostForm('http://ubuntudev/tmgmt/admin/tmgmt/translators/manage/client', $edit, 'Connect');
    $this->assertSession()->pageTextContains('Successfully connected!');
  }
}
