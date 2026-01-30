let selectedUser = 1;
let selectedCurrency = null;

document.addEventListener('DOMContentLoaded', () => {
    fetchUsers();
    fetchDashboard();
});

function fetchUsers() {
    fetch('/api/users')
        .then(r => r.json())
        .then(res => {
            if (!res.success) return alert(res.message);
            const select = document.getElementById('user-select');
            select.innerHTML = '';
            res.data.forEach(u => {
                const opt = document.createElement('option');
                opt.value = u.id;
                opt.textContent = `${u.name} (${u.login})`;
                select.appendChild(opt);
            });
            selectedUser = res.data[0]?.id;
            fetchDashboard();
            select.onchange = () => {
                selectedUser = select.value;
                fetchDashboard();
            };
        });
}

function fetchDashboard() {
    fetch(`/api/dashboard/${selectedUser}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) return alert(res.message);
            renderBalances(res.balances);
            renderCurrencySelect(res.balances);
            renderEvents(res.events);
            renderBets(res.bets, res.events);
        });
}

function renderBalances(balances) {
    const table = document.getElementById('balances');
    table.innerHTML = '<tr><th>Currency</th><th>Amount</th></tr>';
    balances.forEach(b => {
        const row = table.insertRow();
        row.insertCell().innerText = b.currency;
        row.insertCell().innerText = b.amount;
    });
}

function renderEvents(events) {
    const table = document.getElementById('events');
    table.innerHTML = '<tr><th>Event</th><th>Coefficient</th><th>Bet Amount</th><th>Outcome</th><th>Action</th></tr>';
    events.forEach(e => {
        const row = table.insertRow();
        row.insertCell().innerText = e.title;
        row.insertCell().innerText = 2.0;

        const amountCell = row.insertCell();
        const amountInput = document.createElement('input');
        amountInput.type = 'number';
        amountInput.min = '1';
        amountInput.value = '10';
        amountInput.style.width = '60px';
        amountCell.appendChild(amountInput);

        const outcomeCell = row.insertCell();
        const select = document.createElement('select');
        ['home','draw','away'].forEach(o=>{
            const opt = document.createElement('option');
            opt.value=o; opt.textContent=o; select.appendChild(opt);
        });
        outcomeCell.appendChild(select);

        const actionCell = row.insertCell();
        const btn = document.createElement('button');
        btn.innerText = 'Place Bet';
        btn.onclick = ()=>placeBet(e.id, select.value, amountInput.value);
        actionCell.appendChild(btn);
    });
}

function placeBet(eventId, outcome, amount) {
    fetch('/api/bets',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({userId:selectedUser,eventId,outcome,amount,currency:selectedCurrency,coefficient:2.0})
    }).then(r=>r.json()).then(res=>{
        if(!res.success)return toast(res.message);
        toast(`Ставка на ${outcome} (${amount} ${selectedCurrency}) поставлена`);
        fetchDashboard();
    });
}

function renderBets(bets, events) {
    const table = document.getElementById('bets');
    table.innerHTML = '<tr><th>Event</th><th>Outcome</th><th>Amount</th><th>Status</th></tr>';
    const eventMap = {}; events.forEach(e=>eventMap[e.id]=e.title);
    bets.forEach(b=>{
        const row=table.insertRow();
        row.insertCell().innerText=eventMap[b.eventId]||b.eventId;
        row.insertCell().innerText=b.outcome;
        row.insertCell().innerText=b.amount;
        const statusCell=row.insertCell(); statusCell.innerText=b.status;
        if(b.status==='pending')statusCell.style.color='gray';
        if(b.status==='won')statusCell.style.color='green';
        if(b.status==='lost')statusCell.style.color='red';
    });
}

function renderCurrencySelect(balances){
    const select=document.getElementById('currency-select');
    select.innerHTML='';
    balances.forEach(b=>{
        const opt=document.createElement('option');
        opt.value=b.currency;
        opt.textContent=`${b.currency} (${b.amount})`;
        select.appendChild(opt);
    });
    if(!selectedCurrency) selectedCurrency=balances[0]?.currency;
    select.value=selectedCurrency;
    select.onchange=()=>{selectedCurrency=select.value};
}

function toast(message){
    const t=document.createElement('div'); t.className='toast'; t.innerText=message; document.body.appendChild(t);
    setTimeout(()=>t.classList.add('show'),10);
    setTimeout(()=>{t.classList.remove('show'); setTimeout(()=>t.remove(),300)},3000);
}
