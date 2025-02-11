      <?php
        # Retrieve settings from Parameter Store
        error_log('Retrieving settings');
        require 'aws-autoloader.php';
      
        $az = file_get_contents('http://169.254.169.254/latest/meta-data/placement/availability-zone');
        $region = substr($az, 0, -1);
        
        $ssm_client = new Aws\Ssm\SsmClient([
          'version' => 'latest',
          'region'  => $region
        ]);

        try {
          # Retrieve settings from Parameter Store
          $result = $ssm_client->GetParametersByPath(['Path' => '/Bookstore-app/', 'WithDecryption' => true]);

          # Extract individual parameters
          foreach($result['Parameters'] as $p) {
              $values[$p['Name']] = $p['Value'];
          }

          $ep = $values['/BookStore-app/endpoint'];
          $un = $values['/Bookstore-app/username'];
          $pw = $values['/Bookstore-app/password'];
          $db = $values['/Bookstore-app/db'];
        }
        catch (Exception $e) {
          $ep = '';
          $db = '';
          $un = '';
          $pw = '';
        }
      #error_log('Settings are: ' . $ep. " / " . $db . " / " . $un . " / " . $pw);
      ?>
