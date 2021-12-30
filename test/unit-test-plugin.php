<?php

class PluginTest extends TestCase
{
    public function test_plugin_installed() {
        activate_plugin( 'disciple-tools-bulk-contact-messaging/disciple-tools-bulk-contact-messaging.php' );

        $this->assertContains(
            'disciple-tools-bulk-contact-messaging/disciple-tools-bulk-contact-messaging.php',
            get_option( 'active_plugins' )
        );
    }
}
