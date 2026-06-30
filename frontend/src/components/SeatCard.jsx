import React from 'react'

const styles = {
  container: {
    marginTop: '2rem',
    padding: '1.5rem',
    background: 'linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%)',
    borderRadius: '1rem',
    color: '#fff',
    textAlign: 'center',
    boxShadow: '0 8px 32px rgba(30,58,95,0.25)',
  },
  title: {
    fontSize: '1rem',
    fontWeight: 600,
    letterSpacing: '0.08em',
    textTransform: 'uppercase',
    opacity: 0.8,
    marginBottom: '1rem',
  },
  seatRow: {
    display: 'flex',
    justifyContent: 'center',
    gap: '1rem',
    flexWrap: 'wrap',
  },
  seat: {
    background: 'rgba(255,255,255,0.15)',
    border: '2px solid rgba(255,255,255,0.4)',
    borderRadius: '0.75rem',
    padding: '0.75rem 1.5rem',
    fontSize: '2rem',
    fontWeight: 700,
    letterSpacing: '0.05em',
    backdropFilter: 'blur(8px)',
    minWidth: '90px',
  },
  subtitle: {
    marginTop: '1rem',
    fontSize: '0.85rem',
    opacity: 0.7,
  },
}

export default function SeatCard({ seats, details }) {
  return (
    <div style={styles.container}>
      <p style={styles.title}>✈ Voucher Seats Assigned</p>
      <div style={styles.seatRow}>
        {seats.map((seat, i) => (
          <div key={i} style={styles.seat}>
            {seat}
          </div>
        ))}
      </div>
      <p style={styles.subtitle}>
        Flight {details.flightNumber} · {details.flightDate} · {details.aircraftType}
      </p>
    </div>
  )
}
