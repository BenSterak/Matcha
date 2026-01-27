import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from './contexts/AuthContext';
import {
  PrivateRoute,
  GuestRoute,
  EmployerRoute,
  CandidateRoute,
} from './components/common/PrivateRoute';

// Pages
import WelcomePage from './pages/WelcomePage';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import FeedPage from './pages/FeedPage';
import MatchesPage from './pages/MatchesPage';
import ProfilePage from './pages/ProfilePage';

// Onboarding
import OnboardingWizard from './components/features/Onboarding/OnboardingWizard';
import EmployerOnboardingWizard from './components/features/Onboarding/EmployerOnboardingWizard';

// Employer Pages
import EmployerDashboardPage from './pages/employer/EmployerDashboardPage';
import JobPostingPage from './pages/employer/JobPostingPage';
import CandidateReviewPage from './pages/employer/CandidateReviewPage';
import MyJobsPage from './pages/employer/MyJobsPage';

function App() {
  const { isAuthenticated, isEmployer } = useAuth();

  return (
    <Routes>
      {/* Public Routes */}
      <Route
        path="/"
        element={
          <GuestRoute>
            <WelcomePage />
          </GuestRoute>
        }
      />

      <Route
        path="/login"
        element={
          <GuestRoute>
            <LoginPage />
          </GuestRoute>
        }
      />

      <Route
        path="/register"
        element={
          <GuestRoute>
            <RegisterPage />
          </GuestRoute>
        }
      />

      {/* Candidate Onboarding */}
      <Route
        path="/onboarding"
        element={
          <PrivateRoute>
            <OnboardingWizard />
          </PrivateRoute>
        }
      />

      {/* Employer Onboarding */}
      <Route
        path="/onboarding/employer"
        element={
          <PrivateRoute>
            <EmployerOnboardingWizard />
          </PrivateRoute>
        }
      />

      {/* Candidate Routes */}
      <Route
        path="/feed"
        element={
          <CandidateRoute>
            <FeedPage />
          </CandidateRoute>
        }
      />

      <Route
        path="/matches"
        element={
          <PrivateRoute>
            <MatchesPage />
          </PrivateRoute>
        }
      />

      <Route
        path="/profile"
        element={
          <PrivateRoute>
            <ProfilePage />
          </PrivateRoute>
        }
      />

      {/* Employer Routes */}
      <Route
        path="/employer/dashboard"
        element={
          <EmployerRoute>
            <EmployerDashboardPage />
          </EmployerRoute>
        }
      />

      <Route
        path="/employer/jobs"
        element={
          <EmployerRoute>
            <MyJobsPage />
          </EmployerRoute>
        }
      />

      <Route
        path="/employer/jobs/new"
        element={
          <EmployerRoute>
            <JobPostingPage />
          </EmployerRoute>
        }
      />

      <Route
        path="/employer/jobs/:id"
        element={
          <EmployerRoute>
            <JobPostingPage />
          </EmployerRoute>
        }
      />

      <Route
        path="/employer/candidates"
        element={
          <EmployerRoute>
            <CandidateReviewPage />
          </EmployerRoute>
        }
      />

      {/* Catch all - redirect based on auth status */}
      <Route
        path="*"
        element={
          isAuthenticated ? (
            <Navigate to={isEmployer ? '/employer/dashboard' : '/feed'} replace />
          ) : (
            <Navigate to="/" replace />
          )
        }
      />
    </Routes>
  );
}

export default App;
