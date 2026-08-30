import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom';

// Component Island: About Page Interactive Elements
function AboutPage() {
    const [activeTab, setActiveTab] = useState('mission');
    const [staffData, setStaffData] = useState([]);
    const [testimonials, setTestimonials] = useState([]);

    useEffect(() => {
        // Fetch staff and testimonials data
        fetch('/api/about/content')
            .then(response => response.json())
            .then(data => {
                setStaffData(data.staff || []);
                setTestimonials(data.testimonials || []);
            })
            .catch(error => console.error('Error fetching about data:', error));
    }, []);

    const tabs = [
        { id: 'mission', name: 'Mission & Vision', icon: 'bi-bullseye' },
        { id: 'history', name: 'History', icon: 'bi-clock-history' },
        { id: 'staff', name: 'Our Staff', icon: 'bi-people' },
        { id: 'testimonials', name: 'Testimonials', icon: 'bi-star' }
    ];

    const renderTabContent = () => {
        switch(activeTab) {
            case 'mission':
                return (
                    <div className="mission-vision-section">
                        <div className="row g-4">
                            <div className="col-md-6">
                                <div className="card h-100">
                                    <div className="card-body text-center">
                                        <i className="bi bi-bullseye text-primary mb-3" style={{fontSize: '3rem'}}></i>
                                        <h4 className="card-title">Our Mission</h4>
                                        <p className="card-text">
                                            To provide comprehensive education that prepares students for success 
                                            in their academic and professional careers through innovative teaching 
                                            methods and personalized attention.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-6">
                                <div className="card h-100">
                                    <div className="card-body text-center">
                                        <i className="bi bi-eye text-success mb-3" style={{fontSize: '3rem'}}></i>
                                        <h4 className="card-title">Our Vision</h4>
                                        <p className="card-text">
                                            To be a leading educational institution that empowers students to become 
                                            lifelong learners, critical thinkers, and responsible global citizens.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                );
            case 'history':
                return (
                    <div className="history-timeline">
                        <div className="card">
                            <div className="card-body">
                                <div className="timeline">
                                    <div className="timeline-item">
                                        <div className="timeline-marker"></div>
                                        <div className="timeline-content">
                                            <h5>2020</h5>
                                            <p>ILC Learning Center was founded with a vision to provide quality education in General Tinio.</p>
                                        </div>
                                    </div>
                                    <div className="timeline-item">
                                        <div className="timeline-marker"></div>
                                        <div className="timeline-content">
                                            <h5>2022</h5>
                                            <p>Expanded to include junior high school with modern facilities and technology integration.</p>
                                        </div>
                                    </div>
                                    <div className="timeline-item">
                                        <div className="timeline-marker"></div>
                                        <div className="timeline-content">
                                            <h5>2026</h5>
                                            <p>Now serving over 200 students with comprehensive academic programs and extracurricular activities.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                );
            case 'staff':
                return (
                    <div className="staff-section">
                        <div className="row g-4">
                            {staffData.length > 0 ? staffData.map((staff, index) => (
                                <div key={index} className="col-md-4">
                                    <div className="card text-center">
                                        <div className="card-body">
                                            <div className="staff-avatar mb-3">
                                                <i className="bi bi-person-circle" style={{fontSize: '3rem'}}></i>
                                            </div>
                                            <h5 className="card-title">{staff.name}</h5>
                                            <p className="text-muted">{staff.position}</p>
                                            <p className="small">{staff.bio}</p>
                                        </div>
                                    </div>
                                </div>
                            )) : (
                                <div className="col-12 text-center">
                                    <p className="text-muted">Staff information will be available soon.</p>
                                </div>
                            )}
                        </div>
                    </div>
                );
            case 'testimonials':
                return (
                    <div className="testimonials-section">
                        <div className="row g-4">
                            {testimonials.length > 0 ? testimonials.map((testimonial, index) => (
                                <div key={index} className="col-md-6">
                                    <div className="card h-100">
                                        <div className="card-body">
                                            <div className="d-flex mb-3">
                                                <div className="testimonial-avatar me-3">
                                                    <i className="bi bi-person-circle" style={{fontSize: '2rem'}}></i>
                                                </div>
                                                <div>
                                                    <h6 className="mb-1">{testimonial.name}</h6>
                                                    <small className="text-muted">{testimonial.role}</small>
                                                </div>
                                            </div>
                                            <p className="card-text italic">"{testimonial.content}"</p>
                                            <div className="text-warning">
                                                <i className="bi bi-star-fill"></i>
                                                <i className="bi bi-star-fill"></i>
                                                <i className="bi bi-star-fill"></i>
                                                <i className="bi bi-star-fill"></i>
                                                <i className="bi bi-star-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )) : (
                                <div className="col-12 text-center">
                                    <p className="text-muted">Testimonials will be available soon.</p>
                                </div>
                            )}
                        </div>
                    </div>
                );
            default:
                return null;
        }
    };

    return (
        <div className="react-about-page">
            <div className="card">
                <div className="card-header">
                    <h3 className="mb-0">About ILC Learning Center</h3>
                </div>
                <div className="card-body">
                    {/* Tab Navigation */}
                    <ul className="nav nav-tabs mb-4">
                        {tabs.map((tab) => (
                            <li className="nav-item" key={tab.id}>
                                <button
                                    className={`nav-link ${activeTab === tab.id ? 'active' : ''}`}
                                    onClick={() => setActiveTab(tab.id)}
                                >
                                    <i className={`bi ${tab.icon} me-2`}></i>
                                    {tab.name}
                                </button>
                            </li>
                        ))}
                    </ul>

                    {/* Tab Content */}
                    {renderTabContent()}
                </div>
            </div>
        </div>
    );
}

// Mount component when DOM is ready
if (document.getElementById('about-page-react')) {
    const container = document.getElementById('about-page-react');
    const root = createRoot(container);
    root.render(<AboutPage />);
}

export default AboutPage;
