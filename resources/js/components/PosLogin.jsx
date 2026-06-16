import React, { useState } from 'react';

const EMAIL_REQUIRED_MESSAGE = 'Email masih kosong, harap dilengkapi!';
const EMAIL_INVALID_MESSAGE = 'Email yang Anda masukkan salah, silahkan cek kembali!';
const PASSWORD_REQUIRED_MESSAGE = 'Password masih kosong, harap dilengkapi!';

function translateFieldError(field, message) {
    const normalized = message.trim().toLowerCase();

    if (field === 'email') {
        if (normalized.includes('required') || normalized.includes('wajib')) {
            return EMAIL_REQUIRED_MESSAGE;
        }

        if (normalized.includes('email') || normalized.includes('valid')) {
            return EMAIL_INVALID_MESSAGE;
        }
    }

    if (field === 'password' && (normalized.includes('required') || normalized.includes('wajib'))) {
        return PASSWORD_REQUIRED_MESSAGE;
    }

    return message;
}

function validateLoginFields(email, password) {
    const nextErrors = { email: [], password: [] };
    const trimmedEmail = email.trim();

    if (!trimmedEmail) {
        nextErrors.email.push(EMAIL_REQUIRED_MESSAGE);
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmedEmail)) {
        nextErrors.email.push(EMAIL_INVALID_MESSAGE);
    }

    if (!password) {
        nextErrors.password.push(PASSWORD_REQUIRED_MESSAGE);
    }

    return nextErrors;
}

export default function PosLogin({
    action = '/login',
    csrfToken = '',
    oldEmail = '',
    errors = {},
    status = '',
}) {
    const [showPassword, setShowPassword] = useState(false);
    const [email, setEmail] = useState(oldEmail || '');
    const [password, setPassword] = useState('');
    const [remember, setRemember] = useState(false);
    const [clientErrors, setClientErrors] = useState({ email: [], password: [] });

    const loginErrors = errors.login || [];
    const emailErrors = (errors.email || []).map((message) => translateFieldError('email', message));
    const passwordErrors = (errors.password || []).map((message) => translateFieldError('password', message));
    const hasLoginError = loginErrors.length > 0;
    const displayEmailErrors = !hasLoginError && (clientErrors.email.length ? clientErrors.email : emailErrors);
    const displayPasswordErrors = !hasLoginError && (clientErrors.password.length ? clientErrors.password : passwordErrors);

    const handleSubmit = (event) => {
        const nextErrors = validateLoginFields(email, password);

        if (nextErrors.email.length || nextErrors.password.length) {
            event.preventDefault();
            setClientErrors(nextErrors);
            return;
        }

        setClientErrors({ email: [], password: [] });
    };

    const handleEmailChange = (event) => {
        setEmail(event.target.value);

        if (clientErrors.email.length) {
            setClientErrors((prev) => ({ ...prev, email: [] }));
        }
    };

    const handlePasswordChange = (event) => {
        setPassword(event.target.value);

        if (clientErrors.password.length) {
            setClientErrors((prev) => ({ ...prev, password: [] }));
        }
    };

    return (
        <div className="login-page">
            {/* Liquid Glass Background Decorative Blobs */}
            <div className="glass-blob glass-blob--1"></div>
            <div className="glass-blob glass-blob--2"></div>
            <div className="glass-blob glass-blob--3"></div>
            <div className="glass-blob glass-blob--4"></div>

            <div className="login-card-wrapper">
                <div className="card login-card shadow-lg">
                    <div className="card-header login-card__header text-center">
                        <div className="login-brand mb-3">
                            <div className="brand-logo-glow">
                                <BookIconGlow />
                            </div>
                            <span className="brand-badge">KASIR SISTEM</span>
                        </div>
                        <h1 className="login-card__title mb-1">Toko Buku Siswa 2 </h1>
                        <p className="login-card__subtitle">Selamat Datang! Silahkan login untuk mengelola toko.</p>
                    </div>

                    <div className="card-body p-4 p-md-5">
                        {status ? (
                            <div className="alert alert-success py-2 d-flex align-items-center gap-2 border-0 bg-success bg-opacity-10 text-success" role="alert">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m22 4-10 10.01-3-3"/></svg>
                                <span>{status}</span>
                            </div>
                        ) : null}

                        {hasLoginError ? (
                            <div className="login-alert" role="alert" aria-live="polite">
                                <div className="login-alert__icon" aria-hidden="true">
                                    <WarningIcon />
                                </div>
                                <div className="login-alert__content">
                                    <p className="login-alert__title">Login gagal</p>
                                    {loginErrors.map((message, index) => (
                                        <p key={index} className="login-alert__text">
                                            {message}
                                        </p>
                                    ))}
                                </div>
                            </div>
                        ) : null}

                        <form method="POST" action={action} noValidate onSubmit={handleSubmit}>
                            <input type="hidden" name="_token" value={csrfToken} />

                            <div className="mb-4">
                                <label htmlFor="email" className="form-label login-label">
                                    Alamat Email
                                </label>
                                <div className="input-group-custom">
                                    <div className="input-icon">
                                        <MailIcon />
                                    </div>
                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        className={`form-control login-input ${displayEmailErrors.length ? 'login-input--error' : ''}`}
                                        placeholder=""
                                        value={email}
                                        onChange={handleEmailChange}
                                        autoComplete="username"
                                        autoFocus
                                        required
                                        aria-invalid={displayEmailErrors.length > 0}
                                        aria-describedby={displayEmailErrors.length ? 'email-error' : undefined}
                                    />
                                </div>
                                {displayEmailErrors.map((message, index) => (
                                    <p key={index} id={index === 0 ? 'email-error' : undefined} className="login-field-error">
                                        {message}
                                    </p>
                                ))}
                            </div>

                            <div className="mb-4">
                                <label htmlFor="password" className="form-label login-label">
                                    Password
                                </label>
                                <div className="input-group-custom">
                                    <div className="input-icon">
                                        <LockIcon />
                                    </div>
                                    <div className="login-password w-100">
                                        <input
                                            id="password"
                                            type={showPassword ? 'text' : 'password'}
                                            name="password"
                                            className={`form-control login-input login-input--password ${displayPasswordErrors.length ? 'login-input--error' : ''}`}
                                            placeholder="••••••••"
                                            value={password}
                                            onChange={handlePasswordChange}
                                            autoComplete="current-password"
                                            required
                                            aria-invalid={displayPasswordErrors.length > 0}
                                            aria-describedby={displayPasswordErrors.length ? 'password-error' : undefined}
                                        />
                                        <button
                                            type="button"
                                            className="login-password__toggle"
                                            onClick={() => setShowPassword((prev) => !prev)}
                                            aria-label={showPassword ? 'Sembunyikan password' : 'Tampilkan password'}
                                            tabIndex={0}
                                        >
                                            {showPassword ? <EyeIcon /> : <EyeOffIcon />}
                                        </button>
                                    </div>
                                </div>
                                {displayPasswordErrors.map((message, index) => (
                                    <p key={index} id={index === 0 ? 'password-error' : undefined} className="login-field-error">
                                        {message}
                                    </p>
                                ))}
                            </div>

                            <button type="submit" className="btn login-submit w-100 py-3 mt-4">
                                <span>Masuk ke Dashboard</span>
                                <ArrowRightIcon />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
}

function WarningIcon() {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
        >
            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
            <line x1="12" y1="9" x2="12" y2="13" />
            <line x1="12" y1="17" x2="12.01" y2="17" />
        </svg>
    );
}

function BookIconGlow() {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="32"
            height="32"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
        >
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
        </svg>
    );
}

function MailIcon() {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
        >
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
            <polyline points="22,6 12,13 2,6" />
        </svg>
    );
}

function LockIcon() {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
        >
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
        </svg>
    );
}

function ArrowRightIcon() {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2.5"
            strokeLinecap="round"
            strokeLinejoin="round"
            style={{ marginLeft: '8px', transition: 'transform 0.2s ease' }}
            className="arrow-icon"
        >
            <line x1="5" y1="12" x2="19" y2="12" />
            <polyline points="12,5 19,12 12,19" />
        </svg>
    );
}

function EyeIcon() {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.8"
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
        >
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" />
            <circle cx="12" cy="12" r="3" />
        </svg>
    );
}

function EyeOffIcon() {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.8"
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
        >
            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6.5 0-10-7-10-7a18.55 18.55 0 0 1 4.22-5.22" />
            <path d="M9.9 5.08A11 11 0 0 1 12 5c6.5 0 10 7 10 7a18.6 18.6 0 0 1-2.16 3.19" />
            <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24" />
            <line x1="2" y1="2" x2="22" y2="22" />
        </svg>
    );
}
