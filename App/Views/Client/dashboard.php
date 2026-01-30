<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Client Dashboard</title>

    <!-- Общие стили -->
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

<div class="container">

    <h1>Client Dashboard</h1>

    <!-- ===== USER SELECT ===== -->
    <div class="card">
        <select id="user-select"></select>
    </div>

    <!-- ===== BALANCES ===== -->
    <div class="card">
        <h2>Balances</h2>
        <table id="balances"></table>
    </div>

    <div class="card">
        <h2>Active currency</h2>
        <select id="currency-select"></select>
    </div>


    <!-- ===== EVENTS ===== -->
    <div class="card">
        <h2>Events</h2>
        <table id="events"></table>
    </div>

    <!-- ===== BETS ===== -->
    <div class="card">
        <h2>Bets</h2>
        <table id="bets"></table>
    </div>

</div>

<script src="/js/app.js"></script>
</body>
</html>
