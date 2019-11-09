import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.Statement;
import java.sql.ResultSet;
import java.sql.SQLException;

import java.io.Console;
import java.util.Map;
import java.util.HashMap;

import java.util.regex.Pattern;
import java.util.regex.Matcher;
import java.util.regex.PatternSyntaxException;

import org.apache.commons.lang3.StringUtils;

public class DatabaseDemo {

/***** just for fun *****/

	private enum CommandType {
		ALTER ( "alter" ),
		CREATE ( "create" ),
		DELETE ( "delete" ),
		DESC ( "desc" ),
		DROP ( "drop" ),
		EXPLAIN ( "explain" ),
		INSERT ( "insert" ),
		QUIT ( "quit" ),
		SELECT ( "select" ),
		SHOW ( "show" ),
		UPDATE ( "update" ),
		USE ( "use" );

		private final String type;

		CommandType ( String _type ) {
			this.type = _type;
		}

		public String getType () {
			return this.type;
		}
	}

	private Connection conn = null;

	public DatabaseDemo ( String _host, String _user, String _password ) {
		String connString = String.format (
			"jdbc:mysql://%s?user=%s&password=%s",
			_host,
			_user,
			_password
		);

		try {
			this.conn = DriverManager.getConnection ( connString );
			this.runSession ();
		}
		catch ( SQLException ex ) {
			this.exitProgram ( ex.getMessage () );
		}

	}

	private void runSession () {
		Console console = System.console ();

		if ( console == null ) {
			this.exitProgram ( "no console" );
		}
		else {
			this.printWelcome ();
			String cmdString = null;

			while ( ( cmdString = console.readLine ( "YourSQL>> " ) ) != null )
			{
				if ( cmdString.equals ( "" ) )
					continue;

				cmdString = cmdString.trim ();
				String cmdType = getCommandType ( cmdString );

				if ( cmdType == null ) {
					System.err.format ( "Invalid command: '%s'%n", cmdString );
					continue;
				}


				for ( CommandType type : CommandType.values () ) {
					if ( cmdType.equals ( type.getType () ) ) {
						if ( type == CommandType.ALTER ) {
							this.executeUpdate ( cmdString );
						}
						else if ( type == CommandType.CREATE ) {
							this.executeUpdate ( cmdString );
						}
						else if ( type == CommandType.DELETE ) {
							this.executeUpdate ( cmdString );
						}
						else if ( type == CommandType.DESC ) {
                        	this.executeQuery ( cmdString );
						}
						else if ( type == CommandType.DROP ) {
                        	this.executeUpdate ( cmdString );
						}
						else if ( type == CommandType.EXPLAIN ) {
                        	this.executeQuery ( cmdString );
						}
						else if ( type == CommandType.INSERT ) {
                        	this.executeUpdate ( cmdString );
						}
						else if ( type == CommandType.QUIT ) {
							System.out.println ( "bye bye ~ \\^^/" );
							System.exit ( 0 );
						}
						else if ( type == CommandType.SELECT ) {
                        	this.executeQuery ( cmdString );
						}
						else if ( type == CommandType.SHOW ) {
							this.showDBsOrTables ( cmdString );
						}
						else if ( type == CommandType.UPDATE ) {
                        	this.executeUpdate ( cmdString );
						}
						else if ( type == CommandType.USE ) {
							this.selectDB ( cmdString );
						}
						else {
							System.out.format ( "Unknown command: %s%n", cmdString );
						}
					}
				}
			}
		}
	}

	private void printWelcome () {
		String welcomeMsg = "Hi there. Welcome on board.%n%n" +
			"This is a sparring program.%n";
		System.out.format ( welcomeMsg );
	}

	private void exitProgram ( String msg )
	{
		System.err.println ( msg );
		System.exit ( 1 );
	}

	private String getCommandType ( String _cmdStr )
	{
		String cmdWord = this.getCommandWord ( _cmdStr );
		for ( CommandType cmd : CommandType.values () ) {
			String cmdType = cmd.getType ();
			if ( cmdWord.equals ( cmdType ) )
				return cmdType;
		}
		return null;
	}

	private String getCommandWord ( String _cmdStr )
	{
		String delimiter = "\\s+";
		String cmdWord = _cmdStr.split ( delimiter )[ 0 ];
		return cmdWord.toLowerCase ();
	}

	private void executeQuery ( String _cmdStr ) {
		try {
			Statement statement = this.conn.createStatement ();
			ResultSet resultSet = statement.executeQuery ( _cmdStr );
			int colCount = resultSet.getMetaData ().getColumnCount ();
			int rowCount = 0;
			int numRepeats = 20;

			while ( resultSet.next () ) {
				rowCount++;
				System.out.format (
					"%s row. %d %s%n",
					StringUtils.repeat ( "*", numRepeats ),
					rowCount,
					StringUtils.repeat ( "*", numRepeats )
				);

				for ( int iii = 1; iii <= colCount; iii++ ) {
					System.out.format ( "%s:\t", resultSet.getMetaData ().getColumnName ( iii ) );
					System.out.println ( resultSet.getString ( iii ) );
				}
				System.out.println ();
			}
			resultSet.close ();

		}
		catch ( SQLException e ) {
			System.err.println ( e.getMessage () );
		}
	}

	private void executeUpdate ( String _cmdStr ) {
		try {
			int retval = this.conn.prepareStatement ( _cmdStr ).executeUpdate ();
			System.out.format ( "Query OK. %d rows affected%n", retval );
		}
		catch ( Exception e ) {
			System.err.println ( e.getMessage () );
		}
	}

	private void selectDB ( String _cmdStr ) {
		try {
			this.conn.createStatement ().executeQuery ( _cmdStr );
			System.out.println ( "database changed" );
		}
		catch (Exception e ) {
			System.err.println ( e.getMessage () );
		}
	}

	private void showDBsOrTables ( String _cmdStr ) {
		try {
			ResultSet resultSet = this.conn.createStatement ().executeQuery ( _cmdStr );

			if ( resultSet.getMetaData ().getColumnCount () > 0 ) {
				int numRepeat = 20;

				this.drawBorder ( numRepeat );
				System.out.format ( "%s%n", resultSet.getMetaData ().getColumnName ( 1 ) );
				this.drawBorder ( numRepeat );

				int rowCount = 0;
				while ( resultSet.next () ) {
					rowCount++;
					System.out.format ( "%s%n", resultSet.getString ( 1 ) );
				}
				this.drawBorder ( numRepeat );
				System.out.format ( "%d rows in set%n%n", rowCount );
				resultSet.close ();
			}

		}
		catch ( Exception e ) {
			System.err.println ( e.getMessage () );
		}
	}

	private void drawBorder ( int _numRepeat ) {
		System.out.print ( "+" );
		for ( int iii = 0; iii < _numRepeat; iii++ )
			System.out.print ( "-" );
		System.out.println ( "+" );
	}

	private static class DatabaseLogin {

		private static final String[] OPTIONS = new String[] {
			"--help",
			"-h",
			"--host",
			"-u",
			"--user",
			"-p",
			"--password"
		};

		private static final int OP_H_INDEX		= 1;
		private static final int OP_HOST_INDEX	= 2;
		private static final int OP_U_INDEX		= 3;
		private static final int OP_USER_INDEX	= 4;
		private static final int OP_P_INDEX		= 5;
		private static final int OP_PASSWORD_INDEX = 6;

        private static final String USAGE =
            "usage: java DatabaseDemo -h <hostname> -u <username> -p";


		public static final String HOST = "host";
		public static final String USER = "user";
		public static final String PASSWORD = "password";


		private Map<String, String> loginInfo;


		public DatabaseLogin ( String[] _args ) {

			if ( _args.length == 0 ) {
				this.exitApplication ( DatabaseLogin.USAGE, 0 );
			}

			this.extractLoginInfo ( _args );
		}

		private void exitApplication ( String message, int errCode ) {
			System.err.println ( message );
			System.exit ( errCode );
		}


		private void extractLoginInfo ( String[] _args ) {

			this.loginInfo = new HashMap<String, String>();
			this.loginInfo.put ( DatabaseLogin.HOST, null );
			this.loginInfo.put ( DatabaseLogin.USER, null );
			this.loginInfo.put ( DatabaseLogin.PASSWORD, null );

			for ( int iii = 0; iii < _args.length; iii += 2 ) {

				String option = _args[ iii ];

				if ( option.equals ( "--help" ) ) {
					exitApplication (
						DatabaseLogin.USAGE,
						0
					);
				}

				if ( !checkOption ( option ) ) {
					exitApplication (
						"unknown option '" + option + "'",
						1
					);
				}

				if (
					option.equals (
						DatabaseLogin.OPTIONS[ DatabaseLogin.OP_H_INDEX ]
					) ||
					option.equals (
						DatabaseLogin.OPTIONS[ DatabaseLogin.OP_HOST_INDEX ]
					)
				) {
						this.loginInfo.put ( DatabaseLogin.HOST, _args[ iii + 1 ] );
				}
                else if (
                    option.equals (
                        DatabaseLogin.OPTIONS[ DatabaseLogin.OP_U_INDEX ]
                    ) ||
                    option.equals (
                        DatabaseLogin.OPTIONS[ DatabaseLogin.OP_USER_INDEX ]
                    )
                ) {
                        this.loginInfo.put ( DatabaseLogin.USER, _args[ iii + 1 ] );
                }
				else if (
                    option.equals (
                        DatabaseLogin.OPTIONS[ DatabaseLogin.OP_P_INDEX ]
                    ) ||
                    option.equals (
                        DatabaseLogin.OPTIONS[ DatabaseLogin.OP_PASSWORD_INDEX ]
                    )
                ) {
                        this.getPassword ();
                }
			}

		}

		private boolean checkOption ( String option ) {
			for ( String op : DatabaseLogin.OPTIONS ) {
				if ( option.equals ( op ) )
					return true;
			}
			return false;
		}

		private void getPassword () {
			Console console = System.console ();

			if ( console == null ) {
				this.exitApplication ( "{Console Error}", 1 );
			}

			char[] passwordArray = console.readPassword ( "Enter password: " );
			String passwordString = new String ( passwordArray );
			this.loginInfo.put ( DatabaseLogin.PASSWORD, passwordString );

		}

		public String getInfoValue ( String key ) {
			return this.loginInfo.get ( key );
		}

		public void clearInfo () {
			this.loginInfo.clear ();
		}

	}

	public static void main ( String[] args ) {

		DatabaseLogin login = new DatabaseLogin ( args );
		DatabaseDemo demo = new DatabaseDemo (
			login.getInfoValue ( "host" ),
			login.getInfoValue ( "user" ),
			login.getInfoValue ( "password" )
		);

	}

}
