import React, { useState } from 'react';
import { X, CheckCircle, CalendarDays, Clock, Users, Send } from 'lucide-react';
import pb from '@/lib/pocketbaseClient';
import { useLanguage } from '@/context/LanguageContext';

export default function ReservationModal({ onClose }) {
  const { t } = useLanguage();
  const [form, setForm] = useState({
    full_name: '', email: '', phone: '',
    reservation_date: '', reservation_time: '', guests: '', notes: '',
  });
  const [submitting, setSubmitting] = useState(false);
  const [done, setDone] = useState(false);
  const set = (k) => (e) => setForm((p) => ({ ...p, [k]: e.target.value }));

  const submit = async (e) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      await pb.collection('reservations').create({ ...form, guests: parseInt(form.guests, 10) });
      setDone(true);
    } catch (err) {
      console.error(err);
    } finally {
      setSubmitting(false);
    }
  };

  const inputCls = 'w-full px-4 py-3 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-[#5e4743] bg-white';

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" onClick={onClose}>
      <div className="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" onClick={(e) => e.stopPropagation()}>
        <div className="flex items-center justify-between p-6 border-b" style={{ borderColor: '#f0e8d8' }}>
          <div>
            <h3 className="font-typewriter font-bold text-xl" style={{ color: '#5e4743' }}>{t('res_title')}</h3>
            <p className="text-sm text-neutral-500 mt-1">{t('res_subtitle')}</p>
          </div>
          <button onClick={onClose} className="p-2 rounded-full hover:bg-neutral-100"><X size={20} /></button>
        </div>
        <div className="p-6">
          {done ? (
            <div className="text-center py-8">
              <CheckCircle size={52} className="mx-auto mb-4" style={{ color: '#5e4743' }} />
              <h4 className="font-typewriter font-bold text-xl mb-2">{t('res_success_title')}</h4>
              <p className="text-neutral-500 text-sm">{t('res_success_msg')}</p>
              <button onClick={onClose} className="mt-6 px-6 py-2.5 rounded-full text-sm font-semibold text-white" style={{ backgroundColor: '#5e4743' }}>{t('res_close')}</button>
            </div>
          ) : (
            <form onSubmit={submit} className="flex flex-col gap-4">
              <div>
                <label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('res_name')}</label>
                <input required value={form.full_name} onChange={set('full_name')} className={inputCls} placeholder={t('res_placeholder_name')} />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('res_email')}</label>
                  <input required type="email" value={form.email} onChange={set('email')} className={inputCls} placeholder="email@example.com" />
                </div>
                <div>
                  <label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('res_phone')}</label>
                  <input required value={form.phone} onChange={set('phone')} className={inputCls} placeholder="0901 234 567" />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-medium text-neutral-500 mb-1.5 flex items-center gap-1"><CalendarDays size={12} />{t('res_date')}</label>
                  <input required type="date" value={form.reservation_date} onChange={set('reservation_date')} className={inputCls} min={new Date().toISOString().split('T')[0]} />
                </div>
                <div>
                  <label className="block text-xs font-medium text-neutral-500 mb-1.5 flex items-center gap-1"><Clock size={12} />{t('res_time')}</label>
                  <select required value={form.reservation_time} onChange={set('reservation_time')} className={inputCls}>
                    <option value="">{t('res_time_placeholder')}</option>
                    {['09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30','21:00','21:30'].map(ti => (
                      <option key={ti} value={ti}>{ti}</option>
                    ))}
                  </select>
                </div>
              </div>
              <div>
                <label className="block text-xs font-medium text-neutral-500 mb-1.5 flex items-center gap-1"><Users size={12} />{t('res_guests')}</label>
                <select required value={form.guests} onChange={set('guests')} className={inputCls}>
                  <option value="">{t('res_guests_placeholder')}</option>
                  {[1,2,3,4,5,6,7,8,9,10,15,20,'20+'].map(n => (
                    <option key={n} value={n === '20+' ? 21 : n}>{n === '20+' ? t('res_guests_over') : `${n} ${t('res_guests_unit')}`}</option>
                  ))}
                </select>
              </div>
              <div>
                <label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('res_notes')}</label>
                <textarea value={form.notes} onChange={set('notes')} rows={3} className={inputCls} placeholder={t('res_notes_placeholder')} />
              </div>
              <button type="submit" disabled={submitting}
                className="flex items-center justify-center gap-2 py-3.5 rounded-full font-semibold text-sm text-white transition-opacity disabled:opacity-60 mt-2"
                style={{ backgroundColor: '#5e4743' }}>
                {submitting ? t('res_sending') : <><Send size={15} /> {t('res_submit')}</>}
              </button>
              <p className="text-center text-xs text-neutral-400">{t('res_hours_note')}</p>
            </form>
          )}
        </div>
      </div>
    </div>
  );
}
