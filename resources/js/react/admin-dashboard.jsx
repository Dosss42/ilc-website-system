import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, PieChart, Pie, Cell } from 'recharts';

// Component Island: Admin Dashboard Charts
function AdminDashboardCharts() {
    const [stats, setStats] = useState({
        totalStudents: 0,
        totalTeachers: 0,
        enrollmentData: [],
        gradeDistribution: []
    });

    useEffect(() => {
        // Fetch data from Laravel API
        fetch('/api/admin/dashboard-stats')
            .then(response => response.json())
            .then(data => setStats(data))
            .catch(error => console.error('Error fetching dashboard stats:', error));
    }, []);

    const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042'];

    return (
        <div className="react-dashboard-charts">
            <div className="row mb-4">
                <div className="col-md-6">
                    <div className="card">
                        <div className="card-header">
                            <h6 className="mb-0">Enrollment Statistics</h6>
                        </div>
                        <div className="card-body">
                            <BarChart width={400} height={300} data={stats.enrollmentData}>
                                <CartesianGrid strokeDasharray="3 3" />
                                <XAxis dataKey="grade" />
                                <YAxis />
                                <Tooltip />
                                <Legend />
                                <Bar dataKey="students" fill="#8884d8" />
                            </BarChart>
                        </div>
                    </div>
                </div>
                <div className="col-md-6">
                    <div className="card">
                        <div className="card-header">
                            <h6 className="mb-0">Grade Distribution</h6>
                        </div>
                        <div className="card-body">
                            <PieChart width={400} height={300}>
                                <Pie
                                    data={stats.gradeDistribution}
                                    cx={200}
                                    cy={150}
                                    labelLine={false}
                                    label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                                    outerRadius={80}
                                    fill="#8884d8"
                                    dataKey="value"
                                >
                                    {stats.gradeDistribution.map((entry, index) => (
                                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                                    ))}
                                </Pie>
                                <Tooltip />
                            </PieChart>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

// Mount component when DOM is ready
if (document.getElementById('admin-dashboard-charts')) {
    const container = document.getElementById('admin-dashboard-charts');
    const root = createRoot(container);
    root.render(<AdminDashboardCharts />);
}

export default AdminDashboardCharts;
