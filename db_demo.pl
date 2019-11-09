#!/usr/bin/perl -w

use DBI;
use Term::ReadKey;
use Switch;
use strict;
use feature qw/:5.18/;



#--- constants ---#
use constant {
	HOST => 'host',
	USER => 'user',
	PASSWORD => 'password',
	DATABASE => 'database',
};

use constant {
	ALTER => 'alter',
	CREATE => 'create',
	DELETE => 'delete',
	DESC => 'desc',
	DROP => 'drop',
	EXPLAIN => 'explain',
	INSERT => 'insert',
	QUIT => 'quit',
	SELECT => 'select',
	SHOW => 'show',
	UPDATE => 'update',
	USE => 'use'
};

#--- end ---#



#--- variable declarations ---
my ( $clargv, $driver, $dsn, $db_handler, $cmd_string, %login );

$driver = 'mysql';
#--- end ---#



#---------- Main ----------#

# There should be arguments...
if ( scalar ( @ARGV ) == 0 ) {
	print_usage ();
	exit ( 1 );
} 

# put command line arguments into a single string.
$clargv = join ' ', @ARGV;

# beware of argument passed as a reference.
get_login ( $clargv, \%login );

# check the validity of the login.
if ( !validate_login ( %login ) ) {
	print_usage ();
	exit ( 1 );
}

# get the password.
$login { PASSWORD () } = get_password ();
$dsn = "DBI:$driver:" . ( exists ( $login{ DATABASE () } ) ? $login{ DATABASE () } : '' ) .
	':' . $login{ HOST () };

$db_handler = DBI->connect ( $dsn, $login{ USER () }, $login{ PASSWORD () } ) or die DBI::errstr;


while ( 1 ) {
	print "YourSQL>> ";
	chomp ( $cmd_string = <STDIN> );
	next if $cmd_string eq '';

	my $cmd_type = get_command_type ( $cmd_string );

	last if $cmd_type eq QUIT ();

	my $sth = $db_handler->prepare ( qq { $cmd_string } );
	if ( !$sth->execute () ) {
		next;
	}

	switch ( $cmd_type ) {
		case DESC () {
			display_result ( $sth );
		}
		case SELECT () {
			display_result ( $sth );
		}
		case SHOW () {
			display_result ( $sth );
		}
		case USE () {
			say "Database changed.";
		}
		else {
			say "Query OK";
			say $sth->rows . ' rows affected';
		}
	}

}

$db_handler->disconnect or warn $db_handler->errstr;
say 'Bye~';


#----------- end ----------#



#--- sub routines ---#

# prints the usage message when an invalid command is detected.
sub print_usage {
	say 'Usage: perl dbdemo.pl -h <host> -u <user> -p [-A <database>]';
}

# Needless to explain...
sub get_password {

	my $password = undef;
	print 'password: ';
	ReadMode 'noecho';
	chomp ( $password = <STDIN> );
	ReadMode 0;
	print "\n";
	return $password;
}


# not in use ############### 
#sub construct_argv {
#	my $argv = "";
#
#	for ( @_ ) {
#		$argv .= $_ . ' ';
#	}
#	return $argv;
#}
############################

# extract option values from the command line argument.
# extracted values are stored into the hash reference.
sub get_login {
	my $arg = shift;
	my $login = shift;	# hash reference.

	# this hash should be treated as constant.
	my %options = (
		-h => HOST (),
		-u => USER (),
		-p => PASSWORD (),
		-A => DATABASE ()
	);


	# get the option values.
	# pattern -> e.g. /-h\s+(?<host>\w+)/
	for my $key ( keys %options ) {
		my $pattern = qr/${key}\s+(?<${options{$key}}>\w+)/;
		if ( $arg =~ $pattern ) {
			$$login{$options{$key}} = $+{$options{$key}};
		}
	}

}

# check to see if the login is valid
# host and user name are required to be considered to be a valid login
sub validate_login {
	my %login = @_;

	if ( exists $login{ HOST () } && exists $login{ USER () } ) {
		return 1;
	}
	return 0;
}

sub get_command_type {
	my $cmdstr = shift;
	my $pattern = qr/^\s*?(?<cmdtype>\w+)/;

	if ( $cmdstr =~ $pattern ) {
		return lc $+{cmdtype};
	}
	return '';
}

sub display_result {
	my $sth = shift;

	my $fields = $sth->{NAME};

	my $rowcount = 1;
	while ( my $hashref = $sth->fetchrow_hashref ) {
		say '*' x 20 . "row. $rowcount " . '*' x 30;
		$rowcount++;

		for my $field ( @$fields ) {
			say $field . ":\t" . $$hashref{$field} if defined $$hashref{$field};
		}
		say $sth->rows . " rows in set\n";
	}
}


#--- eof ---#
