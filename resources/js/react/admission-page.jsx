import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom';

// Component Island: Admission Page Interactive Elements
function AdmissionPage() {
    const [activeSection, setActiveSection] = useState('requirements');
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        grade: '',
        message: ''
    });
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [submitStatus, setSubmitStatus] = useState('');

    const sections = [
        { id: 'requirements', name: 'Requirements', icon: 'bi-clipboard-check' },
        { id: 'process', name: 'Process', icon: 'bi-arrow-right-circle' },
        { id: 'fees', name: 'Fees', icon: 'bi-cash-stack' },
        { id: 'inquiry', name: 'Inquiry Form', icon: 'bi-envelope' }
    ];

    const requirements = [
        'Birth Certificate (NSO)',
        'Form 138 (Report Card)',
        'Certificate of Good Moral Character',
        '2x2 ID Picture (2 copies)',
        'Parent/Guardian Valid ID',
        'Accomplished Application Form'
    ];

    const processSteps = [
        { step: 1, title: 'Submit Requirements', description: 'Complete and submit all required documents' },
        { step: 2, title: 'Assessment Test', description: 'Take the placement examination' },
        { step: 3, title: 'Interview', description: 'Student and parent interview with the registrar' },
        { step: 4, title: 'Enrollment', description: 'Complete enrollment process and payment' }
    ];

    const feeStructure = [
        { item: 'Tuition Fee', amount: '15,000', period: 'per year' },
        { item: 'Registration Fee', amount: '500', period: 'one-time' },
        { item: 'Laboratory Fee', amount: '2,000', period: 'per year' },
        { item: 'Library Fee', amount: '1,000', period: 'per year' },
        { item: 'Miscellaneous', amount: '3,000', period: 'per year' }
    ];

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
            const response = await fetch('/api/admission/inquiry', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(formData)
            });
            
            if (response.ok) {
                setSubmitStatus('success');
                setFormData({ name: '', email: '', phone: '', grade: '', message: '' });
            } else {
                setSubmitStatus('error');
            }
        } catch (error) {
            setSubmitStatus('error');
        }
        
        setIsSubmitting(false);
        setTimeout(() => setSubmitStatus(''), 3000);
    };

    const renderSectionContent = () => {
        switch(activeSection) {
            case 'requirements':
                return (
                    <div className="requirements-section">
                        <div className="card">
                            <div className="card-body">
                                <h5 className="card-title mb-4">Admission Requirements</h5>
                                <div className="row g-3">
                                    {requirements.map((req, index) => (
                                        <div key={index} className="col-md-6">
                                            <div className="d-flex align-items-center">
                                                <div className="requirement-check me-3">
                                                    <i className="bi bi-check-circle text-success"></i>
                                                </div>
                                                <span>{req}</span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                <div className="alert alert-info mt-4">
                                    <i className="bi bi-info-circle me-2"></i>
                                    <strong>Note:</strong> All documents must be original copies unless specified otherwise.
                                </div>
                            </div>
                        </div>
                    </div>
                );
            case 'process':
                return (
                    <div className="process-section">
                        <div className="card">
                            <div className="card-body">
                                <h5 className="card-title mb-4">Admission Process</h5>
                                <div className="process-timeline">
                                    {processSteps.map((step, index) => (
                                        <div key={step.step} className="process-item">
                                            <div className="process-step">
                                                <div className="step-number">{step.step}</div>
                                            </div>
                                            <div className="process-content">
                                                <h6>{step.title}</h6>
                                                <p className="text-muted">{step.description}</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                );
            case 'fees':
                return (
                    <div className="fees-section">
                        <div className="card">
                            <div className="card-body">
                                <h5 className="card-title mb-4">Fee Structure</h5>
                                <div className="table-responsive">
                                    <table className="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Amount (PHP)</th>
                                                <th>Period</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {feeStructure.map((fee, index) => (
                                                <tr key={index}>
                                                    <td>{fee.item}</td>
                                                    <td className="text-primary fw-bold">¥{fee.amount}</td>
                                                    <td>{fee.period}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                        <tfoot>
                                            <tr className="table-primary">
                                                <th>Total</th>
                                                <th className="text-primary fw-bold">¥21,500</th>
                                                <th>per year</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div className="alert alert-success mt-4">
                                    <i className="bi bi-info-circle me-2"></i>
                                    <strong>Payment Options:</strong> Installment plans available. Please contact the registrar for details.
                                </div>
                            </div>
                        </div>
                    </div>
                );
            case 'inquiry':
                return (
                    <div className="inquiry-section">
                        <div className="card">
                            <div className="card-body">
                                <h5 className="card-title mb-4">Admission Inquiry</h5>
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
                                            <label className="form-label">Phone Number *</label>
                                            <input
                                                type="tel"
                                                className="form-control"
                                                name="phone"
                                                value={formData.phone}
                                                onChange={handleInputChange}
                                                required
                                            />
                                        </div>
                                        <div className="col-md-6">
                                            <label className="form-label">Grade Level Applying For *</label>
                                            <select
                                                className="form-select"
                                                name="grade"
                                                value={formData.grade}
                                                onChange={handleInputChange}
                                                required
                                            >
                                                <option value="">Select Grade Level</option>
                                                <option value="Grade 7">Grade 7</option>
                                                <option value="Grade 8">Grade 8</option>
                                                <option value="Grade 9">Grade 9</option>
                                                <option value="Grade 10">Grade 10</option>
                                            </select>
                                        </div>
                                        <div className="col-12">
                                            <label className="form-label">Message</label>
                                            <textarea
                                                className="form-control"
                                                name="message"
                                                value={formData.message}
                                                onChange={handleInputChange}
                                                rows="4"
                                                placeholder="Any additional questions or information..."
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
                                                        Submitting...
                                                    </>
                                                ) : (
                                                    <>
                                                        <i className="bi bi-send me-2"></i>
                                                        Submit Inquiry
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
                                                Your inquiry has been submitted successfully! We'll contact you soon.
                                            </>
                                        ) : (
                                            <>
                                                <i className="bi bi-exclamation-circle me-2"></i>
                                                Error submitting inquiry. Please try again.
                                            </>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                );
            default:
                return null;
        }
    };

    return (
        <div className="react-admission-page">
            <div className="card">
                <div className="card-header">
                    <h3 className="mb-0">Admission Information</h3>
                </div>
                <div className="card-body">
                    {/* Tab Navigation */}
                    <ul className="nav nav-tabs mb-4">
                        {sections.map((section) => (
                            <li className="nav-item" key={section.id}>
                                <button
                                    className={`nav-link ${activeSection === section.id ? 'active' : ''}`}
                                    onClick={() => setActiveSection(section.id)}
                                >
                                    <i className={`bi ${section.icon} me-2`}></i>
                                    {section.name}
                                </button>
                            </li>
                        ))}
                    </ul>

                    {/* Tab Content */}
                    {renderSectionContent()}
                </div>
            </div>
        </div>
    );
}

// Mount component when DOM is ready
if (document.getElementById('admission-page-react')) {
    const container = document.getElementById('admission-page-react');
    const root = createRoot(container);
    root.render(<AdmissionPage />);
}

export default AdmissionPage;
