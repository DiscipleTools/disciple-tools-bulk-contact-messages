<?php

class PluginTest extends TestCase
{
    public function test_plugin_installed() {
        activate_plugin( 'disciple-tools-bulk-contact-messages/disciple-tools-bulk-contact-messages.php' );

        $this->assertContains(
            'disciple-tools-bulk-contact-messages/disciple-tools-bulk-contact-messages.php',
            get_option( 'active_plugins' )
        );
    }
}
