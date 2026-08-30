import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom';

// Component Island: Academics Page Interactive Elements
function AcademicsPage() {
    const [selectedGrade, setSelectedGrade] = useState('all');
    const [selectedSubject, setSelectedSubject] = useState('all');
    const [curriculumData, setCurriculumData] = useState([]);
    const [programs, setPrograms] = useState([]);

    useEffect(() => {
        // Fetch curriculum and programs data
        fetch('/api/academics/content')
            .then(response => response.json())
            .then(data => {
                setCurriculumData(data.curriculum || []);
                setPrograms(data.programs || []);
            })
            .catch(error => console.error('Error fetching academics data:', error));
    }, []);

    const grades = ['all', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];
    const subjects = ['all', 'Mathematics', 'English', 'Science', 'Filipino', 'Social Studies', 'MAPEH', 'TLE'];

    const filteredCurriculum = curriculumData.filter(item => {
        const gradeMatch = selectedGrade === 'all' || item.grade === selectedGrade;
        const subjectMatch = selectedSubject === 'all' || item.subject === selectedSubject;
        return gradeMatch && subjectMatch;
    });

    return (
        <div className="react-academics-page">
            {/* Academic Programs */}
            <div className="card mb-4">
                <div className="card-header">
                    <h4 className="mb-0">Academic Programs</h4>
                </div>
                <div className="card-body">
                    <div className="row g-4">
                        {programs.length > 0 ? programs.map((program, index) => (
                            <div key={index} className="col-md-4">
                                <div className="card h-100 text-center program-card">
                                    <div className="card-body">
                                        <div className="program-icon mb-3">
                                            <i className={`bi ${program.icon}`} style={{fontSize: '2.5rem', color: program.color}}></i>
                                        </div>
                                        <h5 className="card-title">{program.name}</h5>
                                        <p className="card-text small">{program.description}</p>
                                        <button className="btn btn-outline-primary btn-sm">Learn More</button>
                                    </div>
                                </div>
                            </div>
                        )) : (
                            // Default programs if no data
                            <>
                                <div className="col-md-4">
                                    <div className="card h-100 text-center">
                                        <div className="card-body">
                                            <i className="bi bi-mortarboard text-primary mb-3" style={{fontSize: '2.5rem'}}></i>
                                            <h5 className="card-title">Basic Education</h5>
                                            <p className="card-text small">Comprehensive K-10 curriculum with focus on foundational skills</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-4">
                                    <div className="card h-100 text-center">
                                        <div className="card-body">
                                            <i className="bi bi-palette text-success mb-3" style={{fontSize: '2.5rem'}}></i>
                                            <h5 className="card-title">Arts & Culture</h5>
                                            <p className="card-text small">Developing creativity and cultural appreciation</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-4">
                                    <div className="card h-100 text-center">
                                        <div className="card-body">
                                            <i className="bi bi-trophy text-warning mb-3" style={{fontSize: '2.5rem'}}></i>
                                            <h5 className="card-title">Sports Development</h5>
                                            <p className="card-text small">Physical education and competitive sports programs</p>
                                        </div>
                                    </div>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </div>

            {/* Curriculum Explorer */}
            <div className="card">
                <div className="card-header">
                    <h4 className="mb-0">Curriculum Explorer</h4>
                </div>
                <div className="card-body">
                    {/* Filters */}
                    <div className="row g-3 mb-4">
                        <div className="col-md-6">
                            <label className="form-label">Grade Level</label>
                            <select 
                                className="form-select"
                                value={selectedGrade}
                                onChange={(e) => setSelectedGrade(e.target.value)}
                            >
                                {grades.map(grade => (
                                    <option key={grade} value={grade}>
                                        {grade === 'all' ? 'All Grades' : grade}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="col-md-6">
                            <label className="form-label">Subject</label>
                            <select 
                                className="form-select"
                                value={selectedSubject}
                                onChange={(e) => setSelectedSubject(e.target.value)}
                            >
                                {subjects.map(subject => (
                                    <option key={subject} value={subject}>
                                        {subject === 'all' ? 'All Subjects' : subject}
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>

                    {/* Curriculum Grid */}
                    <div className="row g-3">
                        {filteredCurriculum.length > 0 ? filteredCurriculum.map((item, index) => (
                            <div key={index} className="col-md-6">
                                <div className="card curriculum-item">
                                    <div className="card-body">
                                        <div className="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 className="card-title">{item.subject}</h6>
                                                <p className="text-muted small mb-2">{item.grade}</p>
                                                <p className="card-text small">{item.description}</p>
                                            </div>
                                            <div className="text-end">
                                                <span className="badge bg-primary">{item.units} units</span>
                                                <div className="mt-2">
                                                    <small className="text-muted">
                                                        <i className="bi bi-clock me-1"></i>
                                                        {item.hours} hours/week
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )) : (
                            <div className="col-12 text-center">
                                <p className="text-muted">No curriculum items found for the selected filters.</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

// Mount component when DOM is ready
if (document.getElementById('academics-page-react')) {
    const container = document.getElementById('academics-page-react');
    const root = createRoot(container);
    root.render(<AcademicsPage />);
}

export default AcademicsPage;
