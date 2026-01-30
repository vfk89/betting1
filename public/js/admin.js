document.addEventListener('DOMContentLoaded',()=>{
    loadUsers(); loadEvents(); loadBets();
});

let usersMap={}; let eventsMap={};

function loadUsers(){
    fetch('/api/users').then(r=>r.json()).then(res=>{
        const s=document.getElementById('admin-user'); s.innerHTML='';
        res.data.forEach(u=>{
            usersMap[u.id]=u.name;
            const o=document.createElement('option');
            o.value=u.id; o.textContent=u.name; s.appendChild(o);
        });
    });
}

function loadEvents(){
    fetch('/api/dashboard/1').then(r=>r.json()).then(res=>{
        const s=document.getElementById('admin-event'); s.innerHTML=''; eventsMap={};
        res.events.forEach(e=>{
            eventsMap[e.id]=e.title;
            const o=document.createElement('option');
            o.value=e.id; o.textContent=e.title; s.appendChild(o);
        });
    });
}

function loadBets(){
    fetch('/api/admin/bets').then(r=>r.json()).then(res=>{
        const table=document.getElementById('admin-bets');
        table.innerHTML='<tr><th>Event</th><th>User</th><th>Outcome</th><th>Amount</th><th>Status</th><th>Action</th></tr>';
        res.data.forEach(b=>{
            const row=table.insertRow();
            row.insertCell().innerText=eventsMap[b.eventId]||b.eventId;
            row.insertCell().innerText=usersMap[b.userId]||b.userId;
            row.insertCell().innerText=b.outcome;
            row.insertCell().innerText=b.amount;
            const statusCell=row.insertCell(); statusCell.innerText=b.status;
            if(b.status==='pending')statusCell.style.color='gray';
            if(b.status==='won')statusCell.style.color='green';
            if(b.status==='lost')statusCell.style.color='red';
            const actionCell=row.insertCell();
            ['won','lost'].forEach(s=>{
                const btn=document.createElement('button'); btn.innerText=s;
                btn.onclick=()=>handleSettleBet(b,s);
                actionCell.appendChild(btn);
            });
        });
    });
}

function handleSettleBet(bet,result){
    if(bet.status!=='pending'){
        if(!confirm(`Ставка уже рассчитана (${bet.status}). Вы уверены, что хотите пересчитать её?`)) return;
    }
    fetch(`/api/admin/bets/${bet.id}/settle`,{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({result})
    }).then(r=>r.json()).then(res=>{
        if(!res.success)return toast(res.message);
        toast(`Ставка #${bet.id} рассчитана как ${result}`);
        loadBets();
    });
}

function updateBalance(){
    const userSelect=document.getElementById('admin-user');
    const currencySelect=document.getElementById('admin-currency');
    const amountInput=document.getElementById('admin-amount');
    fetch('/api/admin/balance',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({userId:userSelect.value,currency:currencySelect.value,amount:amountInput.value})
    }).then(r=>r.json()).then(res=>{
        if(!res.success)return toast(res.message);
        toast('Баланс обновлён');
    });
}

function settleEvent(){
    const eventId=document.getElementById('admin-event').value;
    const outcome=document.getElementById('admin-outcome').value;
    fetch(`/api/admin/events/${eventId}/settle`,{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({outcome})
    }).then(r=>r.json()).then(res=>{
        if(!res.success)return toast(res.message);
        toast('Событие рассчитано');
        loadBets();
    });
}

function toast(message){
    const t=document.createElement('div'); t.className='toast'; t.innerText=message; document.body.appendChild(t);
    setTimeout(()=>t.classList.add('show'),10);
    setTimeout(()=>{t.classList.remove('show'); setTimeout(()=>t.remove(),300)},3000);
}
