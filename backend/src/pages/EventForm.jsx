import React, { useEffect, useState } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { eventService } from '../services/api.js';
import { categories, venues, organizers } from '../config/dummy.js';
import { ArrowLeft, Save, AlertCircle, RefreshCw } from 'lucide-react';

const EventForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const isEditMode = !!id;

  const [formData, setFormData] = useState({
    title: '',
    slug: '',
    category: '',
    venue: '',
    organizer: '',
    description: '',
    thumbnail: '',
    banner: '',
    start_date: '',
    end_date: '',
    status: 'draft',
  });

  const [loading, setLoading] = useState(false);
  const [fetching, setFetching] = useState(false);
  const [error, setError] = useState('');
  const [errors, setErrors] = useState({}); // Field-specific validation errors

  // Helper to format date string to datetime-local value (YYYY-MM-DDTHH:MM)
  const formatDatetimeForInput = (datetimeStr) => {
    if (!datetimeStr) return '';
    try {
      const date = new Date(datetimeStr);
      // Adjust timezone offset
      const tzOffset = date.getTimezoneOffset() * 60000;
      const localISOTime = (new Date(date.getTime() - tzOffset)).toISOString().slice(0, 16);
      return localISOTime;
    } catch (e) {
      console.error(e);
      return '';
    }
  };

  useEffect(() => {
    if (isEditMode) {
      const fetchEvent = async () => {
        try {
          setFetching(true);
          setError('');
          const response = await eventService.getByIdOrSlug(id);
          if (response.status === 'success') {
            const event = response.data;
            setFormData({
              title: event.title || '',
              slug: event.slug || '',
              category: event.category || '',
              venue: event.venue || '',
              organizer: event.organizer || '',
              description: event.description || '',
              thumbnail: event.thumbnail || '',
              banner: event.banner || '',
              start_date: formatDatetimeForInput(event.start_date),
              end_date: formatDatetimeForInput(event.end_date),
              status: event.status || 'draft',
            });
          } else {
            setError('Failed to fetch event data.');
          }
        } catch (err) {
          console.error(err);
          setError('Failed to connect to API server.');
        } finally {
          setFetching(false);
        }
      };
      fetchEvent();
    }
  }, [id, isEditMode]);

  // Handle title change and auto-slugify if not edited manually yet
  const handleTitleChange = (e) => {
    const titleVal = e.target.value;
    setFormData((prev) => {
      const newSlug = titleVal
        .toLowerCase()
        .replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-'); // collapse dashes
      
      return {
        ...prev,
        title: titleVal,
        slug: isEditMode ? prev.slug : newSlug, // only auto-slugify in create mode
      };
    });
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    setErrors({});

    try {
      let response;
      if (isEditMode) {
        response = await eventService.update(id, formData);
      } else {
        response = await eventService.create(formData);
      }

      if (response.status === 'success') {
        navigate(`/events/${response.data.id || id}`);
      } else {
        setError(response.message || 'Failed to save event.');
      }
    } catch (err) {
      console.error(err);
      if (err.response && err.response.data) {
        const resData = err.response.data;
        if (resData.message) {
          setError(resData.message);
        }
        if (resData.errors) {
          setErrors(resData.errors);
        }
      } else {
        setError('Connection error occurred while saving.');
      }
    } finally {
      setLoading(false);
    }
  };

  if (fetching) {
    return (
      <div className="flex items-center justify-center p-12 text-slate-500 text-sm animate-pulse space-y-4">
        <RefreshCw size={24} className="animate-spin mx-auto text-indigo-500" />
        <p>Loading event form data...</p>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      <div className="flex items-center">
        <Link to="/events" className="inline-flex items-center gap-2 text-slate-400 hover:text-white font-semibold transition-colors">
          <ArrowLeft size={16} /> Back to Events
        </Link>
      </div>

      {error && (
        <div className="p-4 bg-red-950/30 border border-red-900/30 text-red-400 rounded-xl flex items-center gap-3 text-sm">
          <AlertCircle size={18} />
          <span>{error}</span>
        </div>
      )}

      <div className="bg-slate-900 border border-slate-800/80 rounded-2xl shadow-xl p-8">
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Title */}
            <div className="md:col-span-2">
              <label className="block text-sm font-semibold text-slate-300 mb-2">Event Title *</label>
              <input
                type="text"
                required
                name="title"
                value={formData.title}
                onChange={handleTitleChange}
                placeholder="e.g. Flutter Mobile Development Workshop"
                className="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-all text-sm"
              />
              {errors.title && <p className="text-red-400 text-xs mt-1.5 font-medium">{errors.title[0]}</p>}
            </div>

            {/* Slug */}
            <div>
              <label className="block text-sm font-semibold text-slate-300 mb-2">Slug *</label>
              <input
                type="text"
                required
                name="slug"
                value={formData.slug}
                onChange={handleChange}
                placeholder="flutter-mobile-development-workshop"
                className="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-all text-sm"
              />
              {errors.slug && <p className="text-red-400 text-xs mt-1.5 font-medium">{errors.slug[0]}</p>}
            </div>

            {/* Category */}
            <div>
              <label className="block text-sm font-semibold text-slate-300 mb-2">Category *</label>
              <select
                required
                name="category"
                value={formData.category}
                onChange={handleChange}
                className="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 focus:outline-none focus:border-indigo-500 transition-all text-sm"
              >
                <option value="">-- Select Category --</option>
                {categories.map((c) => (
                  <option key={c.slug} value={c.name}>{c.name}</option>
                ))}
              </select>
              {errors.category && <p className="text-red-400 text-xs mt-1.5 font-medium">{errors.category[0]}</p>}
            </div>

            {/* Venue */}
            <div>
              <label className="block text-sm font-semibold text-slate-300 mb-2">Venue *</label>
              <select
                required
                name="venue"
                value={formData.venue}
                onChange={handleChange}
                className="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 focus:outline-none focus:border-indigo-500 transition-all text-sm"
              >
                <option value="">-- Select Venue --</option>
                {venues.map((v) => (
                  <option key={v.name} value={v.name}>{v.name} ({v.city})</option>
                ))}
              </select>
              {errors.venue && <p className="text-red-400 text-xs mt-1.5 font-medium">{errors.venue[0]}</p>}
            </div>

            {/* Organizer */}
            <div>
              <label className="block text-sm font-semibold text-slate-300 mb-2">Organizer *</label>
              <select
                required
                name="organizer"
                value={formData.organizer}
                onChange={handleChange}
                className="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 focus:outline-none focus:border-indigo-500 transition-all text-sm"
              >
                <option value="">-- Select Organizer --</option>
                {organizers.map((o) => (
                  <option key={o.name} value={o.name}>{o.name}</option>
                ))}
              </select>
              {errors.organizer && <p className="text-red-400 text-xs mt-1.5 font-medium">{errors.organizer[0]}</p>}
            </div>

            {/* Start Date */}
            <div>
              <label className="block text-sm font-semibold text-slate-300 mb-2">Start Date & Time *</label>
              <input
                type="datetime-local"
                required
                name="start_date"
                value={formData.start_date}
                onChange={handleChange}
                className="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 focus:outline-none focus:border-indigo-500 transition-all text-sm"
              />
              {errors.start_date && <p className="text-red-400 text-xs mt-1.5 font-medium">{errors.start_date[0]}</p>}
            </div>

            {/* End Date */}
            <div>
              <label className="block text-sm font-semibold text-slate-300 mb-2">End Date & Time *</label>
              <input
                type="datetime-local"
                required
                name="end_date"
                value={formData.end_date}
                onChange={handleChange}
                className="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 focus:outline-none focus:border-indigo-500 transition-all text-sm"
              />
              {errors.end_date && <p className="text-red-400 text-xs mt-1.5 font-medium">{errors.end_date[0]}</p>}
            </div>

            {/* Thumbnail URL */}
            <div>
              <label className="block text-sm font-semibold text-slate-300 mb-2">Thumbnail URL *</label>
              <input
                type="url"
                required
                name="thumbnail"
                value={formData.thumbnail}
                onChange={handleChange}
                placeholder="https://images.unsplash.com/... (must start with http/https)"
                className="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-all text-sm"
              />
              {errors.thumbnail && <p className="text-red-400 text-xs mt-1.5 font-medium">{errors.thumbnail[0]}</p>}
            </div>

            {/* Banner URL */}
            <div>
              <label className="block text-sm font-semibold text-slate-300 mb-2">Banner URL *</label>
              <input
                type="url"
                required
                name="banner"
                value={formData.banner}
                onChange={handleChange}
                placeholder="https://images.unsplash.com/... (must start with http/https)"
                className="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-all text-sm"
              />
              {errors.banner && <p className="text-red-400 text-xs mt-1.5 font-medium">{errors.banner[0]}</p>}
            </div>

            {/* Description */}
            <div className="md:col-span-2">
              <label className="block text-sm font-semibold text-slate-300 mb-2">Description</label>
              <textarea
                rows="5"
                name="description"
                value={formData.description}
                onChange={handleChange}
                placeholder="Write detailed event overview here..."
                className="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-all text-sm resize-y"
              />
              {errors.description && <p className="text-red-400 text-xs mt-1.5 font-medium">{errors.description[0]}</p>}
            </div>

            {/* Status */}
            <div>
              <label className="block text-sm font-semibold text-slate-300 mb-2">Initial Status</label>
              <select
                name="status"
                value={formData.status}
                onChange={handleChange}
                className="w-full px-4 py-3 bg-slate-950/80 border border-slate-800 rounded-xl text-slate-100 focus:outline-none focus:border-indigo-500 transition-all text-sm"
              >
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
              </select>
              {errors.status && <p className="text-red-400 text-xs mt-1.5 font-medium">{errors.status[0]}</p>}
            </div>
          </div>

          <div className="flex justify-end gap-4 border-t border-slate-800 pt-6">
            <Link
              to="/events"
              className="px-5 py-2.5 border border-slate-800 hover:bg-slate-800/60 text-slate-300 rounded-xl transition-all text-sm font-bold"
            >
              Cancel
            </Link>
            <button
              type="submit"
              disabled={loading}
              className="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-lg shadow-indigo-600/20 font-bold transition-all text-sm disabled:opacity-55 disabled:cursor-not-allowed"
            >
              <Save size={16} />
              {loading ? 'Saving...' : 'Save Event'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default EventForm;
