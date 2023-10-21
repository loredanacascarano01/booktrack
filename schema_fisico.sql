Schema fisico (MySQL):

CREATE TABLE users (
  user_id INT PRIMARY KEY,
  name VARCHAR(255),
  email VARCHAR(255),
  password VARCHAR(255),
  stato VARCHAR(10)
);

CREATE TABLE genres (
  genre_id INT PRIMARY KEY,
  genre_name VARCHAR(255)
);

CREATE TABLE books (
  book_id INT PRIMARY KEY,
  title VARCHAR(255),
  author VARCHAR(255),
  genre_id INT,
  pages INT,
  image_url VARCHAR(255),
  FOREIGN KEY (genre_id) REFERENCES genres (genre_id)
);

CREATE TABLE library (
  library_id INT PRIMARY KEY,
  user_id INT,
  book_id INT,
  FOREIGN KEY (user_id) REFERENCES users (user_id),
  FOREIGN KEY (book_id) REFERENCES books (book_id)
);

CREATE TABLE wishlist (
  wishlist_id INT PRIMARY KEY,
  book_id INT,
  user_id INT,
  priority INT,
  notes VARCHAR(255),
  FOREIGN KEY (book_id) REFERENCES books (book_id),
  FOREIGN KEY (user_id) REFERENCES users (user_id)
);

CREATE TABLE readings (
  reading_id INT PRIMARY KEY,
  user_id INT,
  book_id INT,
  start_date DATE,
  end_date DATE,
  reading_time INT,
  FOREIGN KEY (user_id) REFERENCES users (user_id),
  FOREIGN KEY (book_id) REFERENCES books (book_id)
);

CREATE TABLE reviews (
  review_id INT PRIMARY KEY,
  reading_id INT,
  rating INT,
  comment VARCHAR(255),
  FOREIGN KEY (reading_id) REFERENCES readings (reading_id)
);
