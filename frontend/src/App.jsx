import React, { useState } from 'react'
import { generateVoucher } from './api'
import SeatCard from './components/SeatCard'

const AIRCRAFT_TYPES = ['ATR', 'Airbus 320', 'Boeing 737 Max']

const initialForm = {
  crewName: '',
  crewId: '',
  flightNumber: '',
  flightDate: '',
  aircraftType: '',
}

// ─── Inline styles ────────────────────────────────────────────────────────────

const s = {
  wrapper: {
    width: '100%',
    maxWidth: '520px',
  },
  header: {
    textAlign: 'center',
    marginBottom: '2rem',
  },
  logo: {
    fontSize: '2.5rem',
    marginBottom: '0.5rem',
  },
  title: {
    fontSize: '1.5rem',
    fontWeight: 700,
    color: '#1e3a5f',
  },
  subtitle: {
    fontSize: '0.9rem',
    color: '#64748b',
    marginTop: '0.25rem',
  },
  card: {
    background: '#fff',
    borderRadius: '1rem',
    padding: '2rem',
    boxShadow: '0 4px 24px rgba(0,0,0,0.08)',
  },
  fieldGroup: {
    marginBottom: '1.25rem',
  },
  label: {
    display: 'block',
    fontSize: '0.85rem',
    fontWeight: 600,
    color: '#374151',
    marginBottom: '0.4rem',
  },
  input: {
    width: '100%',
    padding: '0.65rem 0.9rem',
    border: '1.5px solid #e2e8f0',
    borderRadius: '0.5rem',
    fontSize: '0.95rem',
    outline: 'none',
    transition: 'border-color 0.2s',
    color: '#1a202c',
    background: '#fafafa',
  },
  select: {
    width: '100%',
    padding: '0.65rem 0.9rem',
    border: '1.5px solid #e2e8f0',
    borderRadius: '0.5rem',
    fontSize: '0.95rem',
    outline: 'none',
    color: '#1a202c',
    background: '#fafafa',
    cursor: 'pointer',
  },
  button: {
    width: '100%',
    padding: '0.85rem',
    background: 'linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%)',
    color: '#fff',
    border: 'none',
    borderRadius: '0.6rem',
    fontSize: '1rem',
    fontWeight: 600,
    cursor: 'pointer',
    marginTop: '0.5rem',
    letterSpacing: '0.03em',
    transition: 'opacity 0.2s',
  },
  buttonDisabled: {
    opacity: 0.6,
    cursor: 'not-allowed',
  },
  errorBox: {
    marginTop: '1.25rem',
    padding: '0.9rem 1rem',
    background: '#fef2f2',
    border: '1.5px solid #fca5a5',
    borderRadius: '0.6rem',
    color: '#b91c1c',
    fontSize: '0.9rem',
    lineHeight: 1.5,
  },
  fieldError: {
    fontSize: '0.8rem',
    color: '#dc2626',
    marginTop: '0.25rem',
  },
  divider: {
    border: 'none',
    borderTop: '1px solid #e2e8f0',
    margin: '1.5rem 0',
  },
  row: {
    display: 'grid',
    gridTemplateColumns: '1fr 1fr',
    gap: '1rem',
  },
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

function toApiDate(ddmmyyyy) {
  // Convert DD-MM-YYYY → YYYY-MM-DD for the API
  if (!ddmmyyyy) return ''
  const [day, month, year] = ddmmyyyy.split('-')
  if (!day || !month || !year) return ''
  return `${year}-${month}-${day}`
}

function validateForm(form) {
  const errors = {}
  if (!form.crewName.trim()) errors.crewName = 'Crew name is required.'
  if (!form.crewId.trim()) errors.crewId = 'Crew ID is required.'
  if (!form.flightNumber.trim()) errors.flightNumber = 'Flight number is required.'
  if (!form.flightDate.trim()) {
    errors.flightDate = 'Flight date is required.'
  } else {
    const ddmmyyyy = /^\d{2}-\d{2}-\d{4}$/
    if (!ddmmyyyy.test(form.flightDate)) errors.flightDate = 'Use format DD-MM-YYYY.'
  }
  if (!form.aircraftType) errors.aircraftType = 'Please select an aircraft type.'
  return errors
}

// ─── Component ────────────────────────────────────────────────────────────────

const Field = ({ label, name, type = 'text', placeholder, value, error, onChange }) => (
  <div style={s.fieldGroup}>
    <label style={s.label}>{label}</label>
    <input
      style={{
        ...s.input,
        borderColor: error ? '#fca5a5' : '#e2e8f0',
      }}
      type={type}
      name={name}
      value={value}
      onChange={onChange}
      placeholder={placeholder}
      autoComplete="off"
    />
    {error && <p style={s.fieldError}>{error}</p>}
  </div>
)

export default function App() {
  const [form, setForm]           = useState(initialForm)
  const [fieldErrors, setFieldErrors] = useState({})
  const [loading, setLoading]     = useState(false)
  const [errorMsg, setErrorMsg]   = useState('')
  const [result, setResult]       = useState(null)   // { seats, details }

  const handleChange = (e) => {
    const { name, value } = e.target
    setForm((prev) => ({ ...prev, [name]: value }))
    setFieldErrors((prev) => ({ ...prev, [name]: '' }))
    setErrorMsg('')
    setResult(null)
  }

  const handleSubmit = async () => {
    // Client-side validation
    const errors = validateForm(form)
    if (Object.keys(errors).length > 0) {
      setFieldErrors(errors)
      return
    }

    setLoading(true)
    setErrorMsg('')
    setResult(null)

    const apiDate = toApiDate(form.flightDate)

    try {
      const response = await generateVoucher({
        name:         form.crewName.trim(),
        id:           form.crewId.trim(),
        flightNumber: form.flightNumber.trim(),
        date:         apiDate,
        aircraft:     form.aircraftType,
      })

      setResult({
        seats:   response.data.seats,
        details: response.data.details,
      })
    } catch (err) {
      if (err.response?.status === 409) {
        setErrorMsg(
          err.response.data?.message ||
          `Vouchers have already been generated for flight ${form.flightNumber} on ${form.flightDate}.`
        )
      } else if (err.response?.status === 422) {
        const apiErrors = err.response.data.errors || {}
        const firstMsg = Object.values(apiErrors)[0]?.[0]
        setErrorMsg(firstMsg || err.response.data?.message || 'Validation failed. Please check your input.')
      } else if (err.response) {
        setErrorMsg(
          `Server error (${err.response.status}): ${err.response.data?.message || JSON.stringify(err.response.data)}`
        )
      } else if (err.request) {
        setErrorMsg('Could not reach the server. Please check that the backend is running and try again.')
      } else {
        setErrorMsg(`An unexpected error occurred: ${err.message}`)
      }
    } finally {
      setLoading(false)
    }
  }


  return (
    <div style={s.wrapper}>
      {/* Header */}
      <div style={s.header}>
        <div style={s.logo}>✈️</div>
        <h1 style={s.title}>Voucher Seat Assignment</h1>
        <p style={s.subtitle}>Airline Promotional Campaign</p>
      </div>

      {/* Form Card */}
      <div style={s.card}>
        <div style={s.row}>
          <Field label="Crew Name" name="crewName" placeholder="e.g. Sarah" value={form.crewName} error={fieldErrors.crewName} onChange={handleChange} />
          <Field label="Crew ID"   name="crewId"   placeholder="e.g. 98123" value={form.crewId} error={fieldErrors.crewId} onChange={handleChange} />
        </div>

        <hr style={s.divider} />

        <div style={s.row}>
          <Field label="Flight Number" name="flightNumber" placeholder="e.g. GA102" value={form.flightNumber} error={fieldErrors.flightNumber} onChange={handleChange} />
          <Field label="Flight Date (DD-MM-YYYY)" name="flightDate" placeholder="e.g. 12-07-2025" value={form.flightDate} error={fieldErrors.flightDate} onChange={handleChange} />
        </div>

        {/* Aircraft Type */}
        <div style={s.fieldGroup}>
          <label style={s.label}>Aircraft Type</label>
          <select
            style={{
              ...s.select,
              borderColor: fieldErrors.aircraftType ? '#fca5a5' : '#e2e8f0',
            }}
            name="aircraftType"
            value={form.aircraftType}
            onChange={handleChange}
          >
            <option value="">-- Select aircraft type --</option>
            {AIRCRAFT_TYPES.map((type) => (
              <option key={type} value={type}>{type}</option>
            ))}
          </select>
          {fieldErrors.aircraftType && <p style={s.fieldError}>{fieldErrors.aircraftType}</p>}
        </div>

        {/* Submit */}
        <button
          style={{ ...s.button, ...(loading ? s.buttonDisabled : {}) }}
          onClick={handleSubmit}
          disabled={loading}
        >
          {loading ? '⏳ Processing...' : '🎫 Generate Vouchers'}
        </button>

        {/* Error */}
        {errorMsg && (
          <div style={s.errorBox}>
            ⚠️ {errorMsg}
          </div>
        )}
      </div>

      {/* Result */}
      {result && (
        <SeatCard seats={result.seats} details={result.details} />
      )}
    </div>
  )
}
