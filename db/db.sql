


create table students(
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    name varchar(250) NOT NULL,
    email varchar(250) NOT NULL UNIQUE,
    password varchar(250) NOT NULL,
    department varchar(50) NOT NULL,
    program varchar(50) not NULL,
    gender enum("Male", "Female") NOT NULL,
    address varchar(250) not NULL,
    contact varchar(50) not NULL,
    status enum("active", "not active") not null default "active",
    created_at timestamp default current_timestamp not null
);


create table rooms(
    room_id INT PRIMARY KEY AUTO_INCREMENT,
    room_number INT NOT NULL,
    roomType varchar(100) not null,
    description varchar(250) not null,
    imagePath varchar(250) not null,
    rent_fee DECIMAL(10,2) not null ,
    status varchar(20) not null,
    created_at timestamp default current_timestamp not null
);




create table bookings(
    booking_id int PRIMARY KEY AUTO_INCREMENT,
    student_id int not null,
    room_id int not null,
    booking_date DATETIME not null,
    status varchar(20) not null,
    checkout_date DATETIME null,
    created_at timestamp default current_timestamp not null,
    foreign key(student_id) references students(student_id),
    foreign key(room_id) references rooms(room_id)
);



create table payments(
    payment_id int PRIMARY key AUTO_INCREMENT,
    student_id int not null,
    booking_id int not null,
    amount DECIMAL(10,2) not null,
    payment_date DATETIME not null,
    status varchar(20),
    notes varchar(250),
    created_at timestamp default current_timestamp not null,
    foreign key(student_id) references students(student_id),
    foreign key(booking_id) references bookings(booking_id)
);


create table admin(
    admin_id int PRIMARY key AUTO_INCREMENT,
    name varchar(250) not null,
    email varchar(250) not null UNIQUE,
    password varchar(250) not null,
    created_at timestamp default current_timestamp not null
);