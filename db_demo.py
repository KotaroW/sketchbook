##############################################################
#
# file: dbdemo.py
# MySQL-Terminal-like program
# for Python3
###############################################################

#!/usr/bin/python

import MySQLdb, sys, getopt, getpass, re


# global variables
hostKeyString		= "host"
userKeyString 		= "user"
passwordKeyString 	= "password"



# A class that handles the database operations. 
class Database:
	# SQL Query Types
	# private class variable
	__SQL_CMD_TYPES = [ "create", "drop", "delete", "insert", "quit", "select", "update", "use", "show" ]
	__INDEX_CREATE	= 0
	__INDEX_DROP	= 1
	__INDEX_DELETE	= 2
	__INDEX_INSERT	= 3
	__INDEX_QUIT	= 4
	__INDEX_SELECT	= 5
	__INDEX_UPDATE	= 6
	__INDEX_USE		= 7
	__INDEX_SHOW	= 8

	# argument "logininfo" is a tuple type with a dictionary type in it.
	def __init__ ( self, logininfo ):
		self.dbconnection = None
		self.dbcursor = None
		self.connect ( logininfo )


	def printWelcomeMsg ( self ):
		self.dbcursor.execute ( "select version()" )
		version = self.dbcursor.fetchone ()

		msg = "\n\nYour MySQL Version: "
		msg += str ( version[0]  )
		msg += """\nHello there.

This is a test program which was just created out of curiosity.

This program has been designed to behave just like MySQL Terminal program.
As it is a test program. Some of the basic functionalities,
such as "show" command, are still under development.\n\n"""

		print ( msg )


	# database connection
	def connect ( self, logininfo ):
		try:
			self.dbconnection = MySQLdb.connect ( logininfo[ 0 ][ hostKeyString ], logininfo[ 0 ][ userKeyString ], logininfo[ 0 ][ passwordKeyString ] )
			self.dbcursor = self.dbconnection.cursor ()
			self.printWelcomeMsg ()

		except MySQLdb.Error as e:
			print ( e )
			exit ( 1 )

		self.openSession ()

	def openSession ( self ):
		cmdString = ""

		while True:
			# remove the leading / trailing whitespace(s)
			cmdString = self.getSQLCommand ().strip ()
			cmdType = self.getCommandType ( cmdString )

			# CREATE
			if cmdType == Database.__SQL_CMD_TYPES[ Database.__INDEX_CREATE ]:
				self.executeNonQuery ( cmdString )

			# DELETE
			if cmdType == Database.__SQL_CMD_TYPES[ Database.__INDEX_DELETE ]:
				self.executeNonQuery ( cmdString )

			# DROP
			elif cmdType == Database.__SQL_CMD_TYPES[ Database.__INDEX_DROP ]:
				self.executeNonQuery ( cmdString )

			# INSERT
			elif cmdType == Database.__SQL_CMD_TYPES[ Database.__INDEX_INSERT ]:
				self.executeNonQuery ( cmdString )

			# UPDATE
			elif cmdType == Database.__SQL_CMD_TYPES[ Database.__INDEX_UPDATE ]:
				self.executeNonQuery ( cmdString )

			# SELECT
			elif cmdType == Database.__SQL_CMD_TYPES[ Database.__INDEX_SELECT ]:
				self.executeQuery ( cmdString )

			# SHOW
			elif cmdType == Database.__SQL_CMD_TYPES[ Database.__INDEX_SHOW ]:
				self.executeQuery ( cmdString )

			elif cmdType == Database.__SQL_CMD_TYPES[ Database.__INDEX_USE ]:
				self.selectDB ( cmdString )

			else:
				print ( "invalid sql command: %s" % cmdString )



	def getSQLCommand ( self ):
		cmdString = ""

		while cmdString == "":
			try:
				cmdString = input ( "YourSQL>> " )
			# if ctrl+D pressed
			except EOFError:
				print
				continue
			# if ctrl+C pressed
			except KeyboardInterrupt:
				print
				sys.exit ()
			except Exception as e:
				print ( e )
				sys.exit ( 1 )


		return cmdString


	# method determines what sql command it is
	def getCommandType ( self, cmdString ):
		cmdType = None

		pattern = re.compile ( r'^(\w+)', re.I )
		match = pattern.search ( cmdString )

		if ( match != None ):
			cmdType = match.group ().lower ()

		for sqlCmdType in Database.__SQL_CMD_TYPES:
			if cmdType == sqlCmdType:
				if ( cmdType == Database.__SQL_CMD_TYPES[ Database.__INDEX_QUIT ] ):
					print ( "bye \(^^)/" )
					sys.exit ()

				return sqlCmdType

		return None


	def executeNonQuery ( self, cmdString ):
		try:
			self.dbcursor.execute ( cmdString )

		except MySQLdb.Error as e:
			print ( e )
			self.dbconnection.rollback ()
			return

		except MySQLdb.Warning as e:
			print ( e )
			self.dbconnection.rollback ()
			return

		except Exception as e:
			print ( e )
			self.dbconnection.rollback ()
			return

		self.dbconnection.commit ()
		print ( "Query OK, %d rows affected" % self.dbcursor.rowcount )


	def executeQuery ( self, cmdString ):
		try:
			self.dbcursor.execute ( cmdString )

			numFields = len ( self.dbcursor.description )
			fields = [ field[ 0 ] for field in self.dbcursor.description ]
			datarows = self.dbcursor.fetchall ()

			for rowNum in range ( 0, len ( datarows ) ):
				print ( "%s %d. row %s" % ( "*" * 30,  rowNum + 1, "*" * 30 ) )
				for fieldNum in range ( 0, numFields ):
					field = fields[ fieldNum ]
					value = datarows[ rowNum ][ fieldNum ]
					print ( "%s: %s" % ( field, value ) )
				print ( "%d rows in set" % ( len ( datarows ) ) )


		except MySQLdb.Error as e:
			print ( e )
			return

		except Exception as e:
			print ( e )
			return



	def selectDB ( self, cmdString ):
		dbname = None

		# get db name
		pattern = re.compile ( r"^use\s+(\w+)$" )
		match = pattern.search ( cmdString )

		if match != None:
			dbname = match.group ( 1 )

		if dbname != None:
			try:
				self.dbconnection.select_db ( dbname )

			except MySQLdb.Error as e:
				print ( e )
				return

			except Exception as e:
				print ( Argument )
				return

		else:
			print ("invalid syntax: %s" % cmdString )
			return

		print ("Database changed" )


######## Database Class Difinition Ends HERE ##########


# returning value : dictionary in tuple
def getoption ( programName, argv ):

	host = None		# database host
	user = None		# database username
	password = None

	# error message to be displayed before system exit
	usage = "usage: " + programName + " -h <hostname> -u <username> [-p]"

	try:
		opts, args = getopt.getopt ( argv, "?h:u:p", [ "help", "host=", "user=", "password=" ] )

	except getopt.GetoptError:
		print ( usage  )
		sys.exit ( 1 )

	for opt, arg in opts:
		if opt in ( "-?", "--help" ):
			print ( usage )
			sys.exit ()

		elif opt in ( "-h", "--host" ):
			host = arg

		elif opt in ( "-u", "--user" ):
			user = arg

	# both -h (--host) and -u (--user) arguments are mandatory.
	if ( host == None or user == None ):
		print ( usage )
		sys.exit ( 1 )

	password = getpassword ()

	return ( { hostKeyString : host, userKeyString : user, passwordKeyString : password }, )


# argument - dictionary in tuple
def getpassword ():

	pwd = ""

	while pwd == "":
		try:
			pwd = getpass.getpass ( "password: " )
		except:
			print ()
			sys.exit ()

	return pwd

######### def ends here ##########



########## main ##########


if __name__ == "__main__":
	db = Database ( getoption ( sys.argv[0], sys.argv[1:] ) )
