<template>

<nav class="navbar navbar-expand-lg modern-navbar">

<div class="container-fluid">

<RouterLink class="navbar-brand" to="/">
🎣 Horgász
</RouterLink>

<button
class="navbar-toggler"
type="button"
data-bs-toggle="collapse"
data-bs-target="#menu">

<span class="navbar-toggler-icon"></span>

</button>

<div class="collapse navbar-collapse" id="menu">

<ul class="navbar-nav me-auto">

<li class="nav-item">

<RouterLink class="nav-link" to="/">
🏠 Főoldal
</RouterLink>

</li>

<li class="nav-item">

<RouterLink class="nav-link" to="/fishinglog">
🎣 Napló
</RouterLink>

</li>

<li class="nav-item">

<RouterLink class="nav-link" to="/adatok/users">
👤 Userek
</RouterLink>

</li>

</ul>

</div>

</div>

</nav>

</template>

<style scoped>

.modern-navbar{

background:white;

border-bottom:1px solid #e9ecef;

box-shadow:0 2px 6px rgba(0,0,0,0.05);

}

.nav-link{

font-weight:500;

}

.nav-link.router-link-exact-active{

color:#198754 !important;

font-weight:600;

}

</style>