import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const PILAR_CONFIG = {
    admin: {
        label: 'ADMINISTRACIÓN',
        bg: 'rgba(245,158,11,0.12)',
        border: 'rgba(245,158,11,0.35)',
        color: '#F59E0B',
        glow: 'rgba(245,158,11,0.15)',
    },
    gestor: {
        label: 'GESTIÓN HyS',
        bg: 'rgba(246,146,57,0.12)',
        border: 'rgba(246,146,57,0.35)',
        color: '#F69239',
        glow: 'rgba(246,146,57,0.15)',
    },
    tecnico: {
        label: 'INSPECCIÓN CAMPO',
        bg: 'rgba(99,102,241,0.12)',
        border: 'rgba(99,102,241,0.35)',
        color: '#818CF8',
        glow: 'rgba(99,102,241,0.15)',
    },
};

const ICON_MAP = {
    briefcase: (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
            <rect x="2" y="7" width="20" height="14" rx="2" />
            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
            <line x1="12" y1="12" x2="12" y2="16" />
            <line x1="10" y1="14" x2="14" y2="14" />
        </svg>
    ),
    shield: (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
        </svg>
    ),
    clipboard: (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
            <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
            <line x1="9" y1="12" x2="15" y2="12" />
            <line x1="9" y1="16" x2="13" y2="16" />
        </svg>
    ),
    'document-text': (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <polyline points="14 2 14 8 20 8" />
            <line x1="16" y1="13" x2="8" y2="13" />
            <line x1="16" y1="17" x2="8" y2="17" />
            <polyline points="10 9 9 9 8 9" />
        </svg>
    ),
    'lightning-bolt': (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
        </svg>
    ),
};

function TutorialCard({ item }) {
    const pilar = PILAR_CONFIG[item.pilar] || PILAR_CONFIG.admin;

    return (
        <div
            style={{
                background: 'rgba(22,22,42,0.85)',
                border: '1px solid rgba(255,255,255,0.07)',
                borderRadius: '2rem',
                padding: '2rem',
                display: 'flex',
                flexDirection: 'column',
                gap: '1.25rem',
                position: 'relative',
                overflow: 'hidden',
                transition: 'all 0.3s ease',
                boxShadow: '0 4px 24px rgba(0,0,0,0.3)',
            }}
            className="tutorial-card"
        >
            {/* Glow orb */}
            <div style={{
                position: 'absolute',
                top: '-40px',
                right: '-40px',
                width: '140px',
                height: '140px',
                background: `radial-gradient(circle, ${pilar.glow} 0%, transparent 70%)`,
                pointerEvents: 'none',
            }} />

            {/* Header: icon + pilar badge */}
            <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', gap: '1rem' }}>
                <div style={{
                    width: '52px',
                    height: '52px',
                    borderRadius: '1rem',
                    background: pilar.bg,
                    border: `1px solid ${pilar.border}`,
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    color: pilar.color,
                    flexShrink: 0,
                }}>
                    <div style={{ width: '24px', height: '24px' }}>
                        {ICON_MAP[item.icon] || ICON_MAP.clipboard}
                    </div>
                </div>

                <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-end', gap: '0.4rem' }}>
                    <span style={{
                        fontSize: '7px',
                        fontWeight: '900',
                        letterSpacing: '0.18em',
                        textTransform: 'uppercase',
                        fontStyle: 'italic',
                        padding: '3px 10px',
                        borderRadius: '6px',
                        background: pilar.bg,
                        border: `1px solid ${pilar.border}`,
                        color: pilar.color,
                    }}>
                        {pilar.label}
                    </span>
                    <span style={{
                        fontSize: '8px',
                        fontWeight: '700',
                        color: 'rgba(255,255,255,0.3)',
                        fontStyle: 'italic',
                        letterSpacing: '0.1em',
                    }}>
                        {item.code}
                    </span>
                </div>
            </div>

            {/* Title & subtitle */}
            <div style={{ flex: 1 }}>
                <h3 style={{
                    fontSize: '0.95rem',
                    fontWeight: '900',
                    fontStyle: 'italic',
                    textTransform: 'uppercase',
                    letterSpacing: '-0.02em',
                    color: '#ffffff',
                    marginBottom: '0.4rem',
                    lineHeight: 1.2,
                }}>
                    {item.title}
                </h3>
                <p style={{
                    fontSize: '10px',
                    fontWeight: '700',
                    color: 'rgba(165,180,252,0.55)',
                    fontStyle: 'italic',
                    marginBottom: '0.75rem',
                }}>
                    {item.subtitle}
                </p>
                <p style={{
                    fontSize: '11px',
                    color: 'rgba(199,210,254,0.65)',
                    lineHeight: 1.6,
                    fontWeight: '500',
                }}>
                    {item.description}
                </p>
            </div>

            {/* Footer */}
            <div style={{
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                paddingTop: '1rem',
                borderTop: '1px solid rgba(255,255,255,0.06)',
            }}>
                {/* Status badge */}
                {item.available ? (
                    <span style={{
                        display: 'flex',
                        alignItems: 'center',
                        gap: '5px',
                        fontSize: '9px',
                        fontWeight: '900',
                        fontStyle: 'italic',
                        textTransform: 'uppercase',
                        letterSpacing: '0.12em',
                        color: '#34D399',
                    }}>
                        <span style={{
                            width: '6px',
                            height: '6px',
                            borderRadius: '50%',
                            background: '#34D399',
                            display: 'inline-block',
                            animation: 'pulse 2s infinite',
                        }} />
                        Disponible
                    </span>
                ) : (
                    <span style={{
                        fontSize: '9px',
                        fontWeight: '700',
                        fontStyle: 'italic',
                        color: 'rgba(255,255,255,0.2)',
                        textTransform: 'uppercase',
                        letterSpacing: '0.1em',
                    }}>
                        Próximo Sprint
                    </span>
                )}

                {/* PDF Button */}
                {item.available ? (
                    <a
                        href={route('tutorials.pdf', item.key)}
                        target="_blank"
                        rel="noopener noreferrer"
                        id={`pdf-btn-${item.key}`}
                        style={{
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '7px',
                            padding: '8px 18px',
                            borderRadius: '0.75rem',
                            background: 'linear-gradient(135deg, #F69239, #e67e22)',
                            color: '#fff',
                            fontSize: '9px',
                            fontWeight: '900',
                            fontStyle: 'italic',
                            textTransform: 'uppercase',
                            letterSpacing: '0.12em',
                            textDecoration: 'none',
                            transition: 'all 0.2s ease',
                            boxShadow: '0 4px 12px rgba(246,146,57,0.3)',
                            position: 'relative',
                            overflow: 'hidden',
                        }}
                        className="pdf-btn"
                    >
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                            <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Descargar PDF
                    </a>
                ) : (
                    <span style={{
                        display: 'inline-flex',
                        alignItems: 'center',
                        gap: '7px',
                        padding: '8px 18px',
                        borderRadius: '0.75rem',
                        background: 'rgba(255,255,255,0.04)',
                        border: '1px solid rgba(255,255,255,0.08)',
                        color: 'rgba(255,255,255,0.2)',
                        fontSize: '9px',
                        fontWeight: '900',
                        fontStyle: 'italic',
                        textTransform: 'uppercase',
                        letterSpacing: '0.12em',
                        cursor: 'not-allowed',
                    }}>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        No disponible
                    </span>
                )}
            </div>
        </div>
    );
}

export default function Index({ catalog }) {
    const byPilar = {
        admin: catalog.filter(i => i.pilar === 'admin'),
        gestor: catalog.filter(i => i.pilar === 'gestor'),
        tecnico: catalog.filter(i => i.pilar === 'tecnico'),
    };

    const totalAvailable = catalog.filter(i => i.available).length;

    return (
        <AuthenticatedLayout>
            <Head title="Centro de Ayuda y Manuales" />

            <style>{`
                .tutorial-card:hover {
                    border-color: rgba(255,255,255,0.14) !important;
                    transform: translateY(-3px);
                    box-shadow: 0 12px 40px rgba(0,0,0,0.45) !important;
                }
                .pdf-btn:hover {
                    filter: brightness(1.12);
                    transform: translateY(-1px);
                    box-shadow: 0 6px 20px rgba(246,146,57,0.45) !important;
                }
                @keyframes pulse {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.4; }
                }
                .pilar-section-title {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    margin-bottom: 1.25rem;
                }
            `}</style>

            <div style={{
                minHeight: '100vh',
                background: '#11111D',
                color: '#fff',
                padding: '2rem',
            }}>

                {/* ── Header HUD ── */}
                <div style={{
                    background: 'rgba(22,22,42,0.85)',
                    backdropFilter: 'blur(20px)',
                    border: '1px solid rgba(255,255,255,0.08)',
                    borderRadius: '2.5rem',
                    padding: '2.5rem 3rem',
                    marginBottom: '2.5rem',
                    position: 'relative',
                    overflow: 'hidden',
                }}>
                    <div style={{
                        position: 'absolute',
                        inset: 0,
                        background: 'linear-gradient(135deg, rgba(246,146,57,0.07) 0%, transparent 50%, rgba(99,102,241,0.07) 100%)',
                        pointerEvents: 'none',
                    }} />
                    <div style={{ position: 'relative', zIndex: 1, display: 'flex', flexWrap: 'wrap', alignItems: 'center', justifyContent: 'space-between', gap: '1.5rem' }}>
                        <div>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem', marginBottom: '0.5rem' }}>
                                <span style={{
                                    width: '4px',
                                    height: '36px',
                                    background: '#F69239',
                                    borderRadius: '4px',
                                    boxShadow: '0 0 12px rgba(246,146,57,0.5)',
                                }} />
                                <h1 style={{
                                    fontSize: '1.75rem',
                                    fontWeight: '900',
                                    fontStyle: 'italic',
                                    textTransform: 'uppercase',
                                    letterSpacing: '-0.03em',
                                    color: '#fff',
                                    margin: 0,
                                }}>
                                    Centro de Ayuda &amp; Manuales
                                </h1>
                            </div>
                            <p style={{
                                fontSize: '10px',
                                fontWeight: '700',
                                color: 'rgba(165,180,252,0.4)',
                                textTransform: 'uppercase',
                                letterSpacing: '0.2em',
                                fontStyle: 'italic',
                                marginLeft: '1.25rem',
                            }}>
                                Guías de Uso, Protocolos de Operación y Manuales Descargables en PDF
                            </p>
                        </div>

                        <div style={{
                            display: 'flex',
                            alignItems: 'center',
                            gap: '1rem',
                        }}>
                            <div style={{
                                background: 'rgba(52,211,153,0.1)',
                                border: '1px solid rgba(52,211,153,0.25)',
                                borderRadius: '1rem',
                                padding: '0.75rem 1.25rem',
                                textAlign: 'center',
                            }}>
                                <div style={{ fontSize: '1.4rem', fontWeight: '900', color: '#34D399', lineHeight: 1 }}>{totalAvailable}</div>
                                <div style={{ fontSize: '8px', fontWeight: '700', color: 'rgba(52,211,153,0.6)', textTransform: 'uppercase', letterSpacing: '0.1em', marginTop: '2px' }}>Disponibles</div>
                            </div>
                            <div style={{
                                background: 'rgba(246,146,57,0.1)',
                                border: '1px solid rgba(246,146,57,0.25)',
                                borderRadius: '1rem',
                                padding: '0.75rem 1.25rem',
                                textAlign: 'center',
                            }}>
                                <div style={{ fontSize: '1.4rem', fontWeight: '900', color: '#F69239', lineHeight: 1 }}>{catalog.length}</div>
                                <div style={{ fontSize: '8px', fontWeight: '700', color: 'rgba(246,146,57,0.6)', textTransform: 'uppercase', letterSpacing: '0.1em', marginTop: '2px' }}>Manuales</div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* ── Grid de Tarjetas por Pilar ── */}
                <div style={{ display: 'flex', flexDirection: 'column', gap: '3rem' }}>

                    {/* Administración */}
                    {byPilar.admin.length > 0 && (
                        <section>
                            <div className="pilar-section-title">
                                <span style={{ width: '3px', height: '22px', background: '#F59E0B', borderRadius: '4px', boxShadow: '0 0 8px rgba(245,158,11,0.5)' }} />
                                <h2 style={{ fontSize: '10px', fontWeight: '900', fontStyle: 'italic', textTransform: 'uppercase', letterSpacing: '0.25em', color: 'rgba(255,255,255,0.35)', margin: 0 }}>
                                    Administración Comercial
                                </h2>
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))', gap: '1.25rem' }}>
                                {byPilar.admin.map(item => <TutorialCard key={item.key} item={item} />)}
                            </div>
                        </section>
                    )}

                    {/* Gestión HyS */}
                    {byPilar.gestor.length > 0 && (
                        <section>
                            <div className="pilar-section-title">
                                <span style={{ width: '3px', height: '22px', background: '#F69239', borderRadius: '4px', boxShadow: '0 0 8px rgba(246,146,57,0.5)' }} />
                                <h2 style={{ fontSize: '10px', fontWeight: '900', fontStyle: 'italic', textTransform: 'uppercase', letterSpacing: '0.25em', color: 'rgba(255,255,255,0.35)', margin: 0 }}>
                                    Gestión HyS
                                </h2>
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))', gap: '1.25rem' }}>
                                {byPilar.gestor.map(item => <TutorialCard key={item.key} item={item} />)}
                            </div>
                        </section>
                    )}

                    {/* Inspección de Campo */}
                    {byPilar.tecnico.length > 0 && (
                        <section>
                            <div className="pilar-section-title">
                                <span style={{ width: '3px', height: '22px', background: '#818CF8', borderRadius: '4px', boxShadow: '0 0 8px rgba(99,102,241,0.5)' }} />
                                <h2 style={{ fontSize: '10px', fontWeight: '900', fontStyle: 'italic', textTransform: 'uppercase', letterSpacing: '0.25em', color: 'rgba(255,255,255,0.35)', margin: 0 }}>
                                    Inspección de Campo
                                </h2>
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))', gap: '1.25rem' }}>
                                {byPilar.tecnico.map(item => <TutorialCard key={item.key} item={item} />)}
                            </div>
                        </section>
                    )}
                </div>

                {/* Footer */}
                <div style={{
                    marginTop: '3rem',
                    paddingTop: '1.5rem',
                    borderTop: '1px solid rgba(255,255,255,0.05)',
                    display: 'flex',
                    justifyContent: 'space-between',
                    fontSize: '9px',
                    fontWeight: '700',
                    fontStyle: 'italic',
                    textTransform: 'uppercase',
                    letterSpacing: '0.15em',
                    color: 'rgba(255,255,255,0.2)',
                }}>
                    <span>XamaSafe • Sistema de Gestión de Higiene y Seguridad</span>
                    <span>Documentos Oficiales Controlados</span>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
