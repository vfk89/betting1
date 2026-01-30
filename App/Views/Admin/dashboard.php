<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>

    <!-- Общие стили -->
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

<div class="container">

    <h1>Admin Dashboard</h1>

    <!-- ===== UPDATE BALANCE ===== -->
    <div class="card">
        <h2>Update balance</h2>

        <select id="admin-user"></select>

        <select id="admin-currency">
            <option>USD</option>
            <option>EUR</option>
            <option>RUB</option>
        </select>

        <input id="admin-amount" placeholder="amount">

        <button onclick="updateBalance()">Update</button>
    </div>

    <!-- ===== SETTLE EVENT ===== -->
    <div class="card">
        <h2>Settle Event</h2>

        <select id="admin-event"></select>

        <select id="admin-outcome">
            <option value="home">home</option>
            <option value="draw">draw</option>
            <option value="away">away</option>
        </select>

        <button onclick="settleEvent()">Settle Event</button>
    </div>

    <!-- ===== ALL BETS ===== -->
    <div class="card">
        <h2>All Bets</h2>
        <table id="admin-bets"></table>
    </div>

</div>

<script src="/js/admin.js"></script>
</body>
</html>
