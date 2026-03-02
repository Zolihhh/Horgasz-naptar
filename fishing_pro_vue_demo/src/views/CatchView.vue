
<template>

<div class="grid">

<div class="card" v-for="c in catches" :key="c.id">

<h3>{{c.fish}}</h3>
<p>Súly: {{c.weight }} kg</p>
<p>Hossz: {{c.length }} cm</p>

</div>

</div>

</template>

<script>

export default{

data(){
return{
catches:[
{id:1,fish:'Ponty',weight:4.2,length:65},
{id:2,fish:'Csuka',weight:3.1,length:72},
{id:3,fish:'Harcsa',weight:12,length:140}
]
}
}

}

</script>

<style scoped>
.grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
gap:20px;
padding:40px;
}

.card{
background:rgba(0,0,0,0.5);
padding:20px;
border-radius:15px;
}
</style>
