import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { createRoot } from 'react-dom/client';

// Component Island: Multi-step Enrollment Form
function EnrollmentForm() {
    const [currentStep, setCurrentStep] = useState(1);
    const [formData, setFormData] = useState({});
    const { register, handleSubmit, formState: { errors } } = useForm();

    const steps = [
        { id: 1, title: 'Personal Information', icon: 'bi-person' },
        { id: 2, title: 'Address', icon: 'bi-house' },
        { id: 3, title: 'Guardian Info', icon: 'bi-people' },
        { id: 4, title: 'School Details', icon: 'bi-mortarboard' },
        { id: 5, title: 'Enrollment', icon: 'bi-clipboard-check' }
    ];

    const nextStep = () => {
        if (currentStep < steps.length) {
            setCurrentStep(currentStep + 1);
        }
    };

    const prevStep = () => {
        if (currentStep > 1) {
            setCurrentStep(currentStep - 1);
        }
    };

    const onSubmit = (data) => {
        setFormData({ ...formData, ...data });
        if (currentStep < steps.length) {
            nextStep();
        } else {
            // Submit to Laravel API
            fetch('/api/enrollment/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Enrollment submitted successfully!');
                    window.location.href = '/student/portal';
                }
            })
            .catch(error => console.error('Error:', error));
        }
    };

    const renderStepContent = () => {
        switch(currentStep) {
            case 1:
                return (
                    <div className="row g-3">
                        <div className="col-md-6">
                            <label className="form-label">First Name</label>
                            <input 
                                {...register('firstName', { required: true })}
                                className={`form-control ${errors.firstName ? 'is-invalid' : ''}`}
                                placeholder="Enter first name"
                            />
                            {errors.firstName && <div className="invalid-feedback">First name is required</div>}
                        </div>
                        <div className="col-md-6">
                            <label className="form-label">Last Name</label>
                            <input 
                                {...register('lastName', { required: true })}
                                className={`form-control ${errors.lastName ? 'is-invalid' : ''}`}
                                placeholder="Enter last name"
                            />
                            {errors.lastName && <div className="invalid-feedback">Last name is required</div>}
                        </div>
                        <div className="col-md-6">
                            <label className="form-label">Email</label>
                            <input 
                                {...register('email', { required: true, pattern: /^\S+@\S+$/i })}
                                className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                                placeholder="Enter email"
                            />
                            {errors.email && <div className="invalid-feedback">Valid email is required</div>}
                        </div>
                        <div className="col-md-6">
                            <label className="form-label">Phone</label>
                            <input 
                                {...register('phone', { required: true })}
                                className={`form-control ${errors.phone ? 'is-invalid' : ''}`}
                                placeholder="Enter phone number"
                            />
                            {errors.phone && <div className="invalid-feedback">Phone is required</div>}
                        </div>
                    </div>
                );
            case 2:
                return (
                    <div className="row g-3">
                        <div className="col-12">
                            <label className="form-label">Street Address</label>
                            <input 
                                {...register('address', { required: true })}
                                className={`form-control ${errors.address ? 'is-invalid' : ''}`}
                                placeholder="Enter street address"
                            />
                            {errors.address && <div className="invalid-feedback">Address is required</div>}
                        </div>
                        <div className="col-md-4">
                            <label className="form-label">City</label>
                            <input 
                                {...register('city', { required: true })}
                                className={`form-control ${errors.city ? 'is-invalid' : ''}`}
                                placeholder="City"
                            />
                            {errors.city && <div className="invalid-feedback">City is required</div>}
                        </div>
                        <div className="col-md-4">
                            <label className="form-label">Province</label>
                            <select {...register('province', { required: true })} className="form-select">
                                <option value="">Select Province</option>
                                <option value="Nueva Ecija">Nueva Ecija</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div className="col-md-4">
                            <label className="form-label">ZIP Code</label>
                            <input 
                                {...register('zipCode', { required: true })}
                                className={`form-control ${errors.zipCode ? 'is-invalid' : ''}`}
                                placeholder="ZIP Code"
                            />
                        </div>
                    </div>
                );
            case 3:
                return (
                    <div className="row g-3">
                        <div className="col-md-6">
                            <label className="form-label">Guardian Name</label>
                            <input 
                                {...register('guardianName', { required: true })}
                                className={`form-control ${errors.guardianName ? 'is-invalid' : ''}`}
                                placeholder="Guardian full name"
                            />
                            {errors.guardianName && <div className="invalid-feedback">Guardian name is required</div>}
                        </div>
                        <div className="col-md-6">
                            <label className="form-label">Relationship</label>
                            <select {...register('guardianRelationship', { required: true })} className="form-select">
                                <option value="">Select Relationship</option>
                                <option value="Parent">Parent</option>
                                <option value="Guardian">Guardian</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div className="col-md-6">
                            <label className="form-label">Guardian Phone</label>
                            <input 
                                {...register('guardianPhone', { 
                                    required: true,
                                    pattern: /^09[0-9]{2}-[0-9]{3}-[0-9]{4}$|^09[0-9]{9}$/
                                })}
                                className={`form-control ${errors.guardianPhone ? 'is-invalid' : ''}`}
                                placeholder="09XX-XXX-XXXX or 09XXXXXXXXX"
                                title="Please enter a valid Philippine mobile number"
                            />
                            {errors.guardianPhone && <div className="invalid-feedback">Please enter a valid Philippine mobile number (09XX-XXX-XXXX or 09XXXXXXXXX)</div>}
                        </div>
                        <div className="col-md-6">
                            <label className="form-label">Guardian Email</label>
                            <input 
                                {...register('guardianEmail', { required: true, pattern: /^\S+@\S+$/i })}
                                className={`form-control ${errors.guardianEmail ? 'is-invalid' : ''}`}
                                placeholder="Guardian email"
                            />
                        </div>
                    </div>
                );
            case 4:
                return (
                    <div className="row g-3">
                        <div className="col-md-6">
                            <label className="form-label">Previous School</label>
                            <input 
                                {...register('previousSchool', { required: true })}
                                className={`form-control ${errors.previousSchool ? 'is-invalid' : ''}`}
                                placeholder="Previous school name"
                            />
                        </div>
                        <div className="col-md-6">
                            <label className="form-label">Last Grade Level</label>
                            <select {...register('lastGradeLevel', { required: true })} className="form-select">
                                <option value="">Select Grade Level</option>
                                <option value="6">Grade 6</option>
                                <option value="7">Grade 7</option>
                                <option value="8">Grade 8</option>
                                <option value="9">Grade 9</option>
                            </select>
                        </div>
                        <div className="col-12">
                            <label className="form-label">Reason for Transfer</label>
                            <textarea {...register('transferReason', { required: true })} className="form-control" rows="3"></textarea>
                        </div>
                    </div>
                );
            case 5:
                return (
                    <div className="row g-3">
                        <div className="col-md-6">
                            <label className="form-label">Grade Level to Enroll</label>
                            <select {...register('gradeLevel', { required: true })} className="form-select">
                                <option value="">Select Grade Level</option>
                                <option value="7">Grade 7</option>
                                <option value="8">Grade 8</option>
                                <option value="9">Grade 9</option>
                                <option value="10">Grade 10</option>
                            </select>
                        </div>
                        <div className="col-md-6">
                            <label className="form-label">School Year</label>
                            <input 
                                {...register('schoolYear', { required: true })}
                                className="form-control"
                                defaultValue="2026-2027"
                                readOnly
                            />
                        </div>
                        <div className="col-12">
                            <div className="form-check">
                                <input {...register('termsAccepted', { required: true })} className="form-check-input" type="checkbox" />
                                <label className="form-check-label">
                                    I agree to the terms and conditions of enrollment
                                </label>
                            </div>
                        </div>
                    </div>
                );
            default:
                return null;
        }
    };

    return (
        <div className="react-enrollment-form">
            <div className="card">
                <div className="card-header">
                    <h5 className="mb-0">Enrollment Application</h5>
                </div>
                <div className="card-body">
                    {/* Progress Steps */}
                    <div className="steps-wrapper mb-4">
                        <div className="d-flex justify-content-between">
                            {steps.map((step) => (
                                <div key={step.id} className={`step-item ${currentStep >= step.id ? 'active' : ''}`}>
                                    <div className="step-circle">
                                        <i className={`bi ${step.icon}`}></i>
                                    </div>
                                    <small className="step-title">{step.title}</small>
                                </div>
                            ))}
                        </div>
                    </div>

                    <form onSubmit={handleSubmit(onSubmit)}>
                        {renderStepContent()}

                        <div className="d-flex justify-content-between mt-4">
                            <button 
                                type="button" 
                                className="btn btn-secondary" 
                                onClick={prevStep}
                                disabled={currentStep === 1}
                            >
                                <i className="bi bi-arrow-left me-2"></i>Previous
                            </button>
                            <button 
                                type="submit" 
                                className="btn btn-primary"
                            >
                                {currentStep === steps.length ? 'Submit Application' : 'Next'}
                                <i className="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}

// Mount component when DOM is ready
if (document.getElementById('enrollment-form-react')) {
    const container = document.getElementById('enrollment-form-react');
    const root = createRoot(container);
    root.render(<EnrollmentForm />);
}

export default EnrollmentForm;
