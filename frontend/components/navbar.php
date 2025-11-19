<!-- ===================================
     NAVIGATION BAR
     =================================== -->
<nav class="navbar">
    <div class="navbar-container">

        <!-- LEFT - Brand -->
        <a href="index.php" class="navbar-brand">Symvan</a>

        <!-- CENTER - Menu -->
        <ul class="navbar-center-menu">
            <li><a href="index.php"class="<?= $activePage=='index' ? 'active' : '' ?>">Home</a></li>
            <li><a href="calendar.php"class="<?= $activePage=='calendar' ? 'active' : '' ?>">Calendar</a></li>
            <li><a href="myevents.php"class="<?= $activePage=='myevents' ? 'active' : '' ?>">My Events</a></li>
            <li><a href="enroll.php"class="<?= $activePage=='enroll' ? 'active' : '' ?>">Browse Events</a></li>
            <li><a href="organization.php"class="<?= $activePage=='organization' ? 'active' : '' ?>">Organizations</a></li>
            <li><a href="create_event.php"class="<?= $activePage=='create_event' ? 'active' : '' ?>">Create Event</a></li>
            <li><a href="planning.php" class="<?= $activePage=='planning' ? 'active' : '' ?>">Planning</a></li>
            <li><a href="profile.php" class="<?= $activePage=='profile' ? 'active' : '' ?>">Profile</a></li>
        </ul>

        <!-- RIGHT - User session -->
        <div class="navbar-right">
            <?php if (isset($_SESSION['email'])): ?>
                <span class="navbar-email">👋 <?= htmlspecialchars($_SESSION['email']) ?></span>
                <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
            <?php endif; ?>
        </div>

    </div>
</nav>