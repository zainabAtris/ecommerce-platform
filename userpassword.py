import sqlite3

def list_users(db_file='users.db'):
    try:
        # Connect to the SQLite database
        conn = sqlite3.connect(db_file)
        cursor = conn.cursor()
        
        # Execute the query to fetch username and password
        cursor.execute("SELECT username, password FROM users")
        users = cursor.fetchall()
        
        # Check if any users are found and display them
        if users:
            print("{:<20} {:<70}".format("Username", "Password Hash"))
            print("-" * 90)
            for username, password in users:
                print("{:<20} {:<70}".format(username, password))
        else:
            print("No users found in the database.")
    
    except sqlite3.Error as e:
        print(f"Database error: {e}")
    
    finally:
        # Ensure the connection is closed
        if conn:
            conn.close()

if __name__ == "__main__":
    list_users()
