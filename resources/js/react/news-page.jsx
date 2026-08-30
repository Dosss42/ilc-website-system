import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom';

// Component Island: News Page Interactive Elements
function NewsPage() {
    const [newsData, setNewsData] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState('all');
    const [searchTerm, setSearchTerm] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const [loading, setLoading] = useState(true);

    const categories = ['all', 'Events', 'Academic', 'Announcement', 'General', 'Sports'];
    const itemsPerPage = 6;

    useEffect(() => {
        fetchNewsData();
    }, [selectedCategory, currentPage]);

    const fetchNewsData = async () => {
        setLoading(true);
        try {
            const response = await fetch(`/api/news/content?category=${selectedCategory}&page=${currentPage}`);
            const data = await response.json();
            setNewsData(data.news || []);
        } catch (error) {
            console.error('Error fetching news:', error);
            // Set default mock data if API fails
            setNewsData(getMockNewsData());
        } finally {
            setLoading(false);
        }
    };

    const getMockNewsData = () => [
        {
            id: 1,
            title: "ILC Celebrates National Book Month",
            excerpt: "Students participate in various reading activities and book sharing sessions.",
            category: "Academic",
            date: "October 15, 2026",
            image: "/images/news/book-month.jpg",
            author: "Ms. Reyes"
        },
        {
            id: 2,
            title: "Science Fair 2026 Winners Announced",
            excerpt: "Congratulations to our students who won in the regional science fair competition.",
            category: "Academic",
            date: "October 10, 2026",
            image: "/images/news/science-fair.jpg",
            author: "Mr. Santos"
        },
        {
            id: 3,
            title: "Basketball Team Wins Championship",
            excerpt: "Our school basketball team brings home the championship trophy.",
            category: "Sports",
            date: "October 5, 2026",
            image: "/images/news/basketball.jpg",
            author: "Coach Martinez"
        },
        {
            id: 4,
            title: "Enrollment Period Now Open",
            excerpt: "Registration for school year 2026-2027 is now open for new students.",
            category: "Announcement",
            date: "September 28, 2026",
            image: "/images/news/enrollment.jpg",
            author: "Admin Office"
        },
        {
            id: 5,
            title: "Teacher's Day Celebration",
            excerpt: "Students show appreciation to their teachers through various activities.",
            category: "Events",
            date: "September 25, 2026",
            image: "/images/news/teachers-day.jpg",
            author: "Student Council"
        },
        {
            id: 6,
            title: "New Computer Lab Opens",
            excerpt: "State-of-the-art computer lab now available for student use.",
            category: "General",
            date: "September 20, 2026",
            image: "/images/news/computer-lab.jpg",
            author: "Tech Department"
        }
    ];

    const filteredNews = newsData.filter(news => {
        const categoryMatch = selectedCategory === 'all' || news.category === selectedCategory;
        const searchMatch = searchTerm === '' || 
            news.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
            news.excerpt.toLowerCase().includes(searchTerm.toLowerCase());
        return categoryMatch && searchMatch;
    });

    const totalPages = Math.ceil(filteredNews.length / itemsPerPage);
    const displayedNews = filteredNews.slice(
        (currentPage - 1) * itemsPerPage,
        currentPage * itemsPerPage
    );

    const handlePageChange = (page) => {
        setCurrentPage(page);
    };

    return (
        <div className="react-news-page">
            {/* Search and Filter */}
            <div className="card mb-4">
                <div className="card-body">
                    <div className="row g-3">
                        <div className="col-md-6">
                            <div className="input-group">
                                <span className="input-group-text">
                                    <i className="bi bi-search"></i>
                                </span>
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Search news..."
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                />
                            </div>
                        </div>
                        <div className="col-md-6">
                            <select
                                className="form-select"
                                value={selectedCategory}
                                onChange={(e) => setSelectedCategory(e.target.value)}
                            >
                                {categories.map(category => (
                                    <option key={category} value={category}>
                                        {category === 'all' ? 'All Categories' : category}
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {/* News Grid */}
            {loading ? (
                <div className="text-center py-5">
                    <div className="spinner-border text-primary" role="status">
                        <span className="visually-hidden">Loading...</span>
                    </div>
                </div>
            ) : (
                <div className="row g-4">
                    {displayedNews.length > 0 ? displayedNews.map(news => (
                        <div key={news.id} className="col-md-6 col-lg-4">
                            <div className="card news-card h-100">
                                <div className="news-image">
                                    <img
                                        src={news.image}
                                        alt={news.title}
                                        onError={(e) => {
                                            e.target.style.display = 'none';
                                            e.target.parentElement.innerHTML = `
                                                <div class="news-placeholder">
                                                    <i class="bi bi-newspaper"></i>
                                                </div>
                                            `;
                                        }}
                                    />
                                </div>
                                <div className="card-body d-flex flex-column">
                                    <div className="news-meta mb-2">
                                        <span className={`badge bg-${getCategoryColor(news.category)} me-2`}>
                                            {news.category}
                                        </span>
                                        <small className="text-muted">{news.date}</small>
                                    </div>
                                    <h5 className="card-title">{news.title}</h5>
                                    <p className="card-text flex-grow-1">{news.excerpt}</p>
                                    <div className="d-flex justify-content-between align-items-center mt-3">
                                        <small className="text-muted">By {news.author}</small>
                                        <button className="btn btn-outline-primary btn-sm">
                                            Read More
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )) : (
                        <div className="col-12 text-center py-5">
                            <i className="bi bi-newspaper text-muted" style={{fontSize: '3rem'}}></i>
                            <p className="text-muted mt-3">No news articles found.</p>
                        </div>
                    )}
                </div>
            )}

            {/* Pagination */}
            {totalPages > 1 && (
                <div className="d-flex justify-content-center mt-4">
                    <nav>
                        <ul className="pagination">
                            <li className={`page-item ${currentPage === 1 ? 'disabled' : ''}`}>
                                <button
                                    className="page-link"
                                    onClick={() => handlePageChange(currentPage - 1)}
                                >
                                    Previous
                                </button>
                            </li>
                            {[...Array(totalPages)].map((_, index) => (
                                <li
                                    key={index + 1}
                                    className={`page-item ${currentPage === index + 1 ? 'active' : ''}`}
                                >
                                    <button
                                        className="page-link"
                                        onClick={() => handlePageChange(index + 1)}
                                    >
                                        {index + 1}
                                    </button>
                                </li>
                            ))}
                            <li className={`page-item ${currentPage === totalPages ? 'disabled' : ''}`}>
                                <button
                                    className="page-link"
                                    onClick={() => handlePageChange(currentPage + 1)}
                                >
                                    Next
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            )}
        </div>
    );
}

function getCategoryColor(category) {
    const colors = {
        'Events': 'success',
        'Academic': 'primary',
        'Announcement': 'warning',
        'General': 'info',
        'Sports': 'danger'
    };
    return colors[category] || 'secondary';
}

// Mount component when DOM is ready
if (document.getElementById('news-page-react')) {
    const container = document.getElementById('news-page-react');
    const root = createRoot(container);
    root.render(<NewsPage />);
}

export default NewsPage;
