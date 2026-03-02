
<template>
<div>

<nav class="menu">

<RouterLink to="/">Főoldal</RouterLink>
<RouterLink to="/weather">Időjárás</RouterLink>
<RouterLink to="/catches">Fogások</RouterLink>

</nav>

<RouterView/>

</div>
</template>

<style>
body{
margin:0;
font-family:Arial;
background:url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e') center/cover fixed;
color:white;
}

.menu{
display:flex;
gap:20px;
padding:20px;
background:rgba(0,0,0,0.6);
}

a{color:white;text-decoration:none}
</style>
