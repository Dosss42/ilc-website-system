import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

// Component Island: Interactive Student Portal
function StudentPortal() {
    const [activeTab, setActiveTab] = useState('dashboard');
    const [studentData, setStudentData] = useState({
        profile: null,
        grades: [],
        schedule: [],
        announcements: []
    });

    useEffect(() => {
        // Fetch student data from Laravel API
        fetch('/api/student/portal-data')
            .then(response => response.json())
            .then(data => setStudentData(data))
            .catch(error => console.error('Error fetching student data:', error));
    }, []);

    const tabs = [
        { id: 'dashboard', name: 'Dashboard', icon: 'bi-speedometer2' },
        { id: 'grades', name: 'Grades', icon: 'bi-graph-up' },
        { id: 'schedule', name: 'Schedule', icon: 'bi-calendar3' },
        { id: 'announcements', name: 'Announcements', icon: 'bi-megaphone' }
    ];

    const renderTabContent = () => {
        switch(activeTab) {
            case 'dashboard':
                return (
                    <div className="student-dashboard">
                        <div className="row g-4">
                            <div className="col-md-4">
                                <div className="card text-center">
                                    <div className="card-body">
                                        <i className="bi bi-person-circle text-primary" style={{fontSize: '3rem'}}></i>
                                        <h6 className="mt-3">Welcome Back!</h6>
                                        <p className="text-muted">{studentData.profile?.name || 'Student'}</p>
                                        <span className="badge bg-success">Grade {studentData.profile?.gradeLevel || 'N/A'}</span>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-8">
                                <div className="card">
                                    <div className="card-header">
                                        <h6 className="mb-0">Quick Stats</h6>
                                    </div>
                                    <div className="card-body">
                                        <div className="row text-center">
                                            <div className="col-4">
                                                <div className="stat-item">
                                                    <h4 className="text-primary">{studentData.grades?.length || 0}</h4>
                                                    <small className="text-muted">Subjects</small>
                                                </div>
                                            </div>
                                            <div className="col-4">
                                                <div className="stat-item">
                                                    <h4 className="text-success">{studentData.schedule?.length || 0}</h4>
                                                    <small className="text-muted">Classes Today</small>
                                                </div>
                                            </div>
                                            <div className="col-4">
                                                <div className="stat-item">
                                                    <h4 className="text-info">{studentData.announcements?.length || 0}</h4>
                                                    <small className="text-muted">New Updates</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                );
            case 'grades':
                return (
                    <div className="grades-section">
                        <div className="card">
                            <div className="card-header">
                                <h6 className="mb-0">Academic Performance</h6>
                            </div>
                            <div className="card-body">
                                <div className="table-responsive">
                                    <table className="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>1st Quarter</th>
                                                <th>2nd Quarter</th>
                                                <th>3rd Quarter</th>
                                                <th>4th Quarter</th>
                                                <th>Average</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {studentData.grades?.map((grade, index) => (
                                                <tr key={index}>
                                                    <td>{grade.subject}</td>
                                                    <td>{grade.q1 || '-'}</td>
                                                    <td>{grade.q2 || '-'}</td>
                                                    <td>{grade.q3 || '-'}</td>
                                                    <td>{grade.q4 || '-'}</td>
                                                    <td>
                                                        <span className="badge bg-primary">
                                                            {grade.average || '-'}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span className={`badge ${grade.average >= 75 ? 'bg-success' : 'bg-warning'}`}>
                                                            {grade.average >= 75 ? 'Passed' : 'Needs Improvement'}
                                                        </span>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                );
            case 'schedule':
                return (
                    <div className="schedule-section">
                        <div className="card">
                            <div className="card-header">
                                <h6 className="mb-0">Class Schedule</h6>
                            </div>
                            <div className="card-body">
                                <div className="schedule-timeline">
                                    {studentData.schedule?.map((classItem, index) => (
                                        <div key={index} className="schedule-item">
                                            <div className="d-flex align-items-center">
                                                <div className="time-badge">
                                                    <small>{classItem.time}</small>
                                                </div>
                                                <div className="class-info flex-grow-1">
                                                    <h6 className="mb-1">{classItem.subject}</h6>
                                                    <small className="text-muted">
                                                <i className="bi bi-geo-alt me-1"></i>
                                                        {classItem.room} | 
                                                        <i className="bi bi-person ms-2 me-1"></i>
                                                        {classItem.teacher}
                                                    </small>
                                                </div>
                                                <div className="status-badge">
                                                    <span className={`badge ${classItem.status === 'ongoing' ? 'bg-success' : 'bg-secondary'}`}>
                                                        {classItem.status}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                );
            case 'announcements':
                return (
                    <div className="announcements-section">
                        <div className="card">
                            <div className="card-header">
                                <h6 className="mb-0">Latest Announcements</h6>
                            </div>
                            <div className="card-body">
                                {studentData.announcements?.map((announcement, index) => (
                                    <div key={index} className="announcement-item mb-3">
                                        <div className="d-flex">
                                            <div className="announcement-date">
                                                <div className="date-badge">
                                                    <span className="day">{announcement.day}</span>
                                                    <span className="month">{announcement.month}</span>
                                                </div>
                                            </div>
                                            <div className="announcement-content flex-grow-1">
                                                <h6 className="mb-1">{announcement.title}</h6>
                                                <p className="text-muted small mb-1">{announcement.content}</p>
                                                <small className="text-muted">
                                                    <i className="bi bi-person me-1"></i>
                                                    {announcement.author} | 
                                                    <i className="bi bi-clock ms-2 me-1"></i>
                                                    {announcement.time}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                );
            default:
                return null;
        }
    };

    return (
        <div className="react-student-portal">
            <div className="card">
                <div className="card-header">
                    <h5 className="mb-0">Student Portal</h5>
                </div>
                <div className="card-body p-0">
                    {/* Tab Navigation */}
                    <div className="portal-tabs">
                        <div className="nav nav-tabs" role="tablist">
                            {tabs.map((tab) => (
                                <button
                                    key={tab.id}
                                    className={`nav-link ${activeTab === tab.id ? 'active' : ''}`}
                                    onClick={() => setActiveTab(tab.id)}
                                >
                                    <i className={`bi ${tab.icon} me-2`}></i>
                                    {tab.name}
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* Tab Content */}
                    <div className="tab-content p-4">
                        {renderTabContent()}
                    </div>
                </div>
            </div>
        </div>
    );
}

// Mount component when DOM is ready
if (document.getElementById('student-portal-react')) {
    const container = document.getElementById('student-portal-react');
    const root = createRoot(container);
    root.render(<StudentPortal />);
}

export default StudentPortal;
