User Stories 1

Epic A — Member Features
US-01 — Register/Login
As a member, I want to create an account and log in, so that I can
access gym services.

Priority: High
Preconditions

User has access to the system
Registration page is available
Trigger
• User opens the system and selects register/login
Primary Flow

User enters required details (name, email, password).

System validates input.

System stores user data in database.

User logs in using credentials.

System verifies credentials.

System grants access to dashboard.

Alternate Flows
• A1: Invalid input o System shows error message
• A2: Wrong credentials o “Invalid username or password”

Acceptance Criteria
• Users can register successfully
• Users can log in securely
• Invalid inputs are handled properly

---

US-02 — View Gym Packages
As a member , I want to view available gym packages, so that i can
choose a plan.

Priority: High
Preconditions

User is logged in.

Packages exist in the system.
Trigger
• User opens packages page.
Primary Flow

System retrieves package list from database.

System displays package name, price, and details.

User selects a package.

System shows full details.
Alternate Flows
• A1: No packages available o System displays message

Acceptance Criteria
• Packages are displayed correctly
• Details are accessible

---

US-03 — Book Gym Package
As a member, I want to book a package so that I can use gym services.

Priority: High
Preconditions

User is logged in
Package is available
Trigger
• User clicks “Book Now”
Primary Flow

User selects package.
User confirms booking.
System records booking in database.
System displays confirmation.
Alternate Flows
• A1: System error o Booking fails and message is shown.
Acceptance Criteria
• Booking is saved successfully
• Confirmation is displayed

---

US-04 — View Booking History
As a member , I want to view my bookings, so that I can track my
activity.
Priority: Medium
Preconditions

User is logged in.
Bookings exist
Trigger
• User opens booking history
Primary Flow

System retrieves booking records.
System displays booking details.
Alternate Flows
• A1: No bookings o Display “No records found”
Acceptance Criteria
• Booking history is accurate
• Data loads correctly

---

Epic B — Admin Features
US-05 — Manage Packages
As an admin, I want to manage gym packages, so that services stay
updated.
Priority: High
Preconditions

Admin is logged in
Admin dashboard is accessible
Trigger
• Admin opens package management
Primary Flow

Admin adds/edits/deletes package.
System validates input.
System saves changes.
System updates package list.
Alternate Flows
• A1: Invalid input o Error message displayed
Acceptance Criteria
• Packages can be added, edited, deleted
• Changes reflect immediately

---

US-06 — Manage Bookings
As an admin , I want to manage bookings, so that I can track gym
usage.
Priority: High
Preconditions

Admin user is authenticated.
Approved import template exists (columns defined).
Trigger
• Admin opens booking management
Primary Flow

Admin views list of bookings.
Admin selects a booking.
System saves changes.
Alternate Flows
• A1: No bookings o Display message

Acceptance Criteria
• Bookings are displayed correctly
• Status updates are saved

---

US-07 — Generate Reports
As an admin, I want to generate reports, so that I can monitor business
performance.
Priority: Medium
Preconditions

Admin is logged in
Trigger
• Admin selects reports

Primary Flow

System retrieves data.
System generates report.
Admin views report.
Alternate Flows
• A1: No Data Available o System displays “No data available for
selected period.”

Acceptance Criteria
• Reports are accurate
• Data is updated

---

US-08 — Approve Member Registration
As an admin, I want to approve member registrations so that I can
prevent spam or fake accounts from accessing the system.
Priority: High
Preconditions
Admin is logged in.
A member has submitted a registration request.
Registration requests are stored in the system.

Trigger
• Admin opens the registration report approval page.

Primary Flow

System retrieves the list of pending member registrations.
System displays member details (name, email, registration date).
Admin reviews the registration information.
Admin selects Approve.
If approved, the member account becomes active and can log in.
Alternate Flows
• A1: No pending registrations
• A2: Admin clicks approve successfully
Acceptance Criteria
• Admin can view all pending registration requests.
• Admin can approve a registration member.
• Approved members can log in to the system.
• Pending members registrations cannot access the system.
As an admin, I want to approve member registrations so that I can
prevent spam or fake accounts from accessing the system.
Priority: High
Preconditions
Admin is logged in.
A member has submitted a registration request.
Registration requests are stored in the system.
Trigger
• Admin opens the registration report approval page.

Primary Flow

System retrieves the list of pending member registrations.
System displays member details (name, email, registration date).
Admin reviews the registration information.
Admin selects Approve.
If approved, the member account becomes active and can log in.
Alternate Flows
• A1: No pending registrations
• A2: Admin clicks approve successfully
Acceptance Criteria
• Admin can view all pending registration requests.
• Admin can approve a registration member.
• Approved members can log in to the system.
• Pending members registrations cannot access the system.
