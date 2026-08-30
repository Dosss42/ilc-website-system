import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom';

// Component Island: Contact Page Interactive Elements
function ContactPage() {
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: '',
        contactMethod: 'email'
    });
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [submitStatus, setSubmitStatus] = useState('');
    const [mapLoaded, setMapLoaded] = useState(false);

    const contactMethods = [
        { value: 'email', label: 'Email', icon: 'bi-envelope' },
        { value: 'phone', label: 'Phone', icon: 'bi-telephone' },
        { value: 'visit', label: 'Visit', icon: 'bi-geo-alt' }
    ];

    const subjects = [
        'General Inquiry',
        'Admission',
        'Academic Questions',
        'Technical Support',
        'Feedback',
        'Other'
    ];

    const contactInfo = [
        {
            icon: 'bi-geo-alt-fill',
            title: 'Address',
            details: ['IEMELIF Learning Center', 'General Tinio, Nueva Ecija', 'Philippines 3120']
        },
        {
            icon: 'bi-telephone-fill',
            title: 'Phone',
            details: ['+63 (44) 123-4567', '+63 912-345-6789 (Mobile)']
        },
        {
            icon: 'bi-envelope-fill',
            title: 'Email',
            details: ['info@ilc-learning.edu.ph', 'admissions@ilc-learning.edu.ph']
        },
        {
            icon: 'bi-clock-fill',
            title: 'Office Hours',
            details: ['Monday - Friday: 7:30 AM - 5:00 PM', 'Saturday: 8:00 AM - 12:00 PM']
        }
    ];

    useEffect(() => {
        // Simulate map loading
        const timer = setTimeout(() => {
            setMapLoaded(true);
        }, 1000);
        return () => clearTimeout(timer);
    }, []);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        
        try {
            const response = await fetch('/api/contact/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(formData)
            });
            
            if (response.ok) {
                setSubmitStatus('success');
                setFormData({
                    name: '',
                    email: '',
                    phone: '',
                    subject: '',
                    message: '',
                    contactMethod: 'email'
                });
            } else {
                setSubmitStatus('error');
            }
        } catch (error) {
            setSubmitStatus('error');
        }
        
        setIsSubmitting(false);
        setTimeout(() => setSubmitStatus(''), 5000);
    };

    return (
        <div className="react-contact-page">
            <div className="row g-4">
                {/* Contact Information */}
                <div className="col-lg-4">
                    <div className="card">
                        <div className="card-header">
                            <h4 className="mb-0">Contact Information</h4>
                        </div>
                        <div className="card-body">
                            {contactInfo.map((info, index) => (
                                <div key={index} className="contact-item mb-4">
                                    <div className="d-flex">
                                        <div className="contact-icon me-3">
                                            <i className={`bi ${info.icon} text-primary`} style={{fontSize: '1.5rem'}}></i>
                                        </div>
                                        <div>
                                            <h6 className="mb-2">{info.title}</h6>
                                            {info.details.map((detail, idx) => (
                                                <small key={idx} className="text-muted d-block">{detail}</small>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Contact Form */}
                <div className="col-lg-8">
                    <div className="card">
                        <div className="card-header">
                            <h4 className="mb-0">Send us a Message</h4>
                        </div>
                        <div className="card-body">
                            <form onSubmit={handleSubmit}>
                                <div className="row g-3">
                                    <div className="col-md-6">
                                        <label className="form-label">Full Name *</label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            name="name"
                                            value={formData.name}
                                            onChange={handleInputChange}
                                            required
                                        />
                                    </div>
                                    <div className="col-md-6">
                                        <label className="form-label">Email Address *</label>
                                        <input
                                            type="email"
                                            className="form-control"
                                            name="email"
                                            value={formData.email}
                                            onChange={handleInputChange}
                                            required
                                        />
                                    </div>
                                    <div className="col-md-6">
                                        <label className="form-label">Phone Number</label>
                                        <input
                                            type="tel"
                                            className="form-control"
                                            name="phone"
                                            value={formData.phone}
                                            onChange={handleInputChange}
                                        />
                                    </div>
                                    <div className="col-md-6">
                                        <label className="form-label">Subject *</label>
                                        <select
                                            className="form-select"
                                            name="subject"
                                            value={formData.subject}
                                            onChange={handleInputChange}
                                            required
                                        >
                                            <option value="">Select Subject</option>
                                            {subjects.map(subject => (
                                                <option key={subject} value={subject}>{subject}</option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="col-12">
                                        <label className="form-label">Preferred Contact Method</label>
                                        <div className="btn-group w-100" role="group">
                                            {contactMethods.map(method => (
                                                <button
                                                    key={method.value}
                                                    type="button"
                                                    className={`btn ${formData.contactMethod === method.value ? 'btn-primary' : 'btn-outline-primary'}`}
                                                    onClick={() => setFormData(prev => ({ ...prev, contactMethod: method.value }))}
                                                >
                                                    <i className={`bi ${method.icon} me-2`}></i>
                                                    {method.label}
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                    <div className="col-12">
                                        <label className="form-label">Message *</label>
                                        <textarea
                                            className="form-control"
                                            name="message"
                                            value={formData.message}
                                            onChange={handleInputChange}
                                            rows="5"
                                            placeholder="Tell us how we can help you..."
                                            required
                                        ></textarea>
                                    </div>
                                    <div className="col-12">
                                        <button
                                            type="submit"
                                            className="btn btn-primary"
                                            disabled={isSubmitting}
                                        >
                                            {isSubmitting ? (
                                                <>
                                                    <span className="spinner-border spinner-border-sm me-2"></span>
                                                    Sending...
                                                </>
                                            ) : (
                                                <>
                                                    <i className="bi bi-send me-2"></i>
                                                    Send Message
                                                </>
                                            )}
                                        </button>
                                    </div>
                                </div>
                            </form>

                            {submitStatus && (
                                <div className={`alert mt-3 ${submitStatus === 'success' ? 'alert-success' : 'alert-danger'}`}>
                                    {submitStatus === 'success' ? (
                                        <>
                                            <i className="bi bi-check-circle me-2"></i>
                                            Message sent successfully! We'll get back to you within 24 hours.
                                        </>
                                    ) : (
                                        <>
                                            <i className="bi bi-exclamation-circle me-2"></i>
                                            Error sending message. Please try again or call us directly.
                                        </>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Map Section */}
            <div className="row mt-4">
                <div className="col-12">
                    <div className="card">
                        <div className="card-header">
                            <h4 className="mb-0">Find Us</h4>
                        </div>
                        <div className="card-body p-0">
                            <div className="map-container" style={{height: '400px', position: 'relative'}}>
                                {!mapLoaded ? (
                                    <div className="d-flex justify-content-center align-items-center h-100">
                                        <div className="text-center">
                                            <div className="spinner-border text-primary mb-3" role="status">
                                                <span className="visually-hidden">Loading map...</span>
                                            </div>
                                            <p className="text-muted">Loading map...</p>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="d-flex justify-content-center align-items-center h-100 bg-light">
                                        <div className="text-center">
                                            <i className="bi bi-geo-alt text-primary" style={{fontSize: '3rem'}}></i>
                                            <h5 className="mt-3">IEMELIF Learning Center</h5>
                                            <p className="text-muted">General Tinio, Nueva Ecija</p>
                                            <small className="text-muted">Interactive map would be displayed here</small>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

// Mount component when DOM is ready
if (document.getElementById('contact-page-react')) {
    const container = document.getElementById('contact-page-react');
    const root = createRoot(container);
    root.render(<ContactPage />);
}

export default ContactPage;
