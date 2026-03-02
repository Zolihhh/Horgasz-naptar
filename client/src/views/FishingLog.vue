<template>
  <div class="fishing-log">

    <!-- Cím -->
    <div class="log-header">
      <h1>🎣 Horgásznapló</h1>
      <p>Rögzítsd és kövesd a fogásaidat modern formában</p>
    </div>

    <!-- Űrlap -->
    <div class="log-form glass">
      <h2>Új fogás hozzáadása</h2>

      <div class="form-grid">
        <input type="date" v-model="newEntry.date" />

        <select v-model="newEntry.fish">
          <option disabled value="">Halfaj kiválasztása</option>
          <option>Ponty</option>
          <option>Csuka</option>
          <option>Harcsa</option>
          <option>Süllő</option>
          <option>Amur</option>
        </select>

        <input type="number" step="0.1" placeholder="Súly (kg)" v-model="newEntry.weight" />

        <input type="text" placeholder="Helyszín" v-model="newEntry.location" />

        <input type="text" placeholder="Időjárás" v-model="newEntry.weather" />
      </div>

      <textarea
        placeholder="Megjegyzés..."
        v-model="newEntry.note"
      ></textarea>

      <button @click="addEntry">Fogás mentése</button>
    </div>

    <!-- Lista -->
    <div class="log-list">
      <div
        v-for="(entry, index) in entries"
        :key="index"
        class="log-card glass"
      >
        <div class="log-top">
          <h3>{{ entry.fish }} - {{ entry.weight }} kg</h3>
          <span>{{ entry.date }}</span>
        </div>

        <p><strong>📍</strong> {{ entry.location }}</p>
        <p><strong>🌤</strong> {{ entry.weather }}</p>
        <p class="note">{{ entry.note }}</p>

        <button class="delete-btn" @click="removeEntry(index)">
          Törlés
        </button>
      </div>
    </div>

  </div>
</template>

<script>
export default {
  name: "FishingLog",
  data() {
    return {
      newEntry: {
        date: "",
        fish: "",
        weight: "",
        location: "",
        weather: "",
        note: ""
      },
      entries: []
    }
  },
  methods: {
    addEntry() {
      if (!this.newEntry.date || !this.newEntry.fish) return;

      this.entries.unshift({ ...this.newEntry });

      this.newEntry = {
        date: "",
        fish: "",
        weight: "",
        location: "",
        weather: "",
        note: ""
      };
    },
    removeEntry(index) {
      this.entries.splice(index, 1);
    }
  }
}
</script>

<style scoped>

.fishing-log {
  padding: 20px;
  color: white;
}

/* Header */
.log-header {
  text-align: center;
  margin-bottom: 30px;
}

.log-header h1 {
  font-size: 2.3rem;
}

/* Glass */
.glass {
  background: rgba(255,255,255,0.08);
  backdrop-filter: blur(15px);
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
  margin-bottom: 25px;
}

/* Form */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 15px;
  margin-bottom: 15px;
}

input, select, textarea {
  padding: 10px;
  border-radius: 12px;
  border: none;
  background: rgba(255,255,255,0.15);
  color: white;
}

textarea {
  width: 100%;
  min-height: 80px;
  margin-bottom: 15px;
}

button {
  padding: 10px 20px;
  border-radius: 12px;
  border: none;
  cursor: pointer;
  font-weight: bold;
  background: linear-gradient(45deg, #00c3ff, #00ff99);
  color: black;
  transition: 0.3s;
}

button:hover {
  transform: scale(1.05);
}

/* Lista */
.log-card {
  transition: 0.3s;
}

.log-card:hover {
  transform: translateY(-5px);
}

.log-top {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.note {
  opacity: 0.8;
  margin: 10px 0;
}

.delete-btn {
  background: linear-gradient(45deg, #ff4d4d, #ff0000);
  color: white;
}

</style>