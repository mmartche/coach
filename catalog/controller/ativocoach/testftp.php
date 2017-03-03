<?php

		$host = "ftp.aws.ativo.com";
		$user = "ftp_system_coach";
		$pass = "dsfbsd8f67f8xqqudfaghfdh";

		$file = 'somefile.txt';
		$remote_file = 'readme.txt';

		// set up basic connection
		$conn_id = ftp_connect($host,'21');
		var_dump($conn_id);
		print_r("<hr />");
		// login with username and password
		$login_result = ftp_login($conn_id, $user, $pass);
		var_dump($login_result);
		// turn passive mode on
		ftp_pasv($conn_id, true);

		// upload a file
		if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
		 echo "successfully uploaded $file\n";
		} else {
		 echo "There was a problem while uploading $file\n";
		}

		// close the connection
		ftp_close($conn_id);
		phpinfo();
	