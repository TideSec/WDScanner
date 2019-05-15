#!/usr/bin/env python
# encoding: utf-8
#Embedded file name: threadpool.py

from threading import Thread, Lock, currentThread, Event, Semaphore
from weakref import ref
import atexit
import time
import traceback
try:
    from queue import Queue, Empty
except ImportError:
    from Queue import Queue, Empty

_threadpools = set()
_G_MAXTHREAD = Semaphore(200)

def _shutdown_all():
    for pool_ref in tuple(_threadpools):
        pool = pool_ref()
        if pool:
            pool.wait()


atexit.register(_shutdown_all)
#队列中等待的任务
TASK_STATUS_QUEUE = 0
#队列中进行的任务
TASK_STATUS_RUNNING = 1
#队列中结束的任务
TASK_STATUS_FINISHED = 2

class ThreadPool(object):

    def __init__(self, max_threads = 20, core_threads = 0, keepalive = 1):
        """
        :param core_threads: maximum number of persistent threads in the pool
        :param max_threads: maximum number of total threads in the pool
        :param keepalive: seconds to keep non-core worker threads waiting
            for new tasks
        """
        #最少线程数
        self.core_threads = core_threads
        #最大线程数
        self.max_threads = max(max_threads, core_threads, 1)
        #等待时间
        self.keepalive = keepalive
        self._queue = Queue()
        self._threads_lock = Lock()
        self._threads = set()
        self._shutdown = False
        self._stop = False
        self._busy = 0
        self._event_dismiss = Event()
        _threadpools.add(ref(self))
    #调整线程
    def _adjust_threadcount(self):
        #如果当前线程数大于最大线程数
        if self.num_threads >= self.max_threads:
            return
        #
        self._threads_lock.acquire()
        try:
            self._add_thread(self.num_threads < self.core_threads)
        except:
            pass
        finally:
            self._threads_lock.release()
    #增加线程数
    def _add_thread(self, core):
        if not _G_MAXTHREAD.acquire(False):
            return
        t = Thread(target=self._run_jobs, args=(core,))
        t.setDaemon(True)
        t.start()
        self._threads.add(t)

    def _run_jobs(self, core):
        block = True
        timeout = None
        if not core:
            block = self.keepalive > 0
            timeout = self.keepalive
        while True:
            is_empty = True
            try:
                func, arg, callback, callback_arg = self._queue.get(block, timeout)
                is_empty = False
            except Empty:
                break

            if is_empty:
                if self._shutdown:
                    break
            elif func:
                if not self._stop:
                    try:
                        if callback:
                            callback(TASK_STATUS_RUNNING, callback_arg)
                        func(arg)
                        if callback:
                            callback(TASK_STATUS_FINISHED, callback_arg)
                    except Exception as e:
                        print 'THREAD:', str(e)
                        print traceback.format_exc()

                self._threads_lock.acquire()
                self._busy -= 1
                self._threads_lock.release()

        self._threads_lock.acquire()
        try:
            self._threads.remove(currentThread())
        finally:
            self._threads_lock.release()

        self._event_dismiss.set()
        _G_MAXTHREAD.release()

    @property
    def num_threads(self):
        return len(self._threads)

    def busy(self):
        return self._busy

    def idel(self):
        return max(0, self.max_threads - self._busy)

    def push(self, func, arg = None, callback = None, callback_arg = None):
        if self._stop:
            return
        self._threads_lock.acquire()
        try:
            self._queue.put((func,
             arg,
             callback,
             callback_arg))
            if func:
                self._busy += 1
            if callback:
                callback(TASK_STATUS_QUEUE, callback_arg)
        finally:
            self._threads_lock.release()

        self._adjust_threadcount()

    def wait_for_idel(self, timeout = None):
        if self.num_threads < self.max_threads:
            time.sleep(0.5)
            return
        if self._event_dismiss.wait(timeout):
            self._event_dismiss.clear()

    def stop(self):
        self._stop = True

    def wait(self, wait = True):
        if self._shutdown:
            return
        self._shutdown = True
        self._stop = not wait
        _threadpools.remove(ref(self))
        self._threads_lock.acquire()
        try:
            for _ in range(self.num_threads):
                self._queue.put((None, None, None, None))

            threads = tuple(self._threads)
        finally:
            self._threads_lock.release()
            for thread in threads:
                thread.join()

    def __repr__(self):
        if self.max_threads:
            threadcount = '%d/%d' % (self.num_threads, self.max_threads)
        else:
            threadcount = '%d' % self.num_threads
        return '<ThreadPool at %x; threads=%s>' % (id(self), threadcount)


if __name__ == '__main__':
    import time
    def worker(arg):
        time.sleep(0.1)
        if arg==10000:
            print arg
            return True
        else:
            return arg

    def callback(status, arg):
        print status, arg


    tp = ThreadPool(5)
    for i in range(50000):
        tp.push(worker, i, callback, 'worker')
        #tp.push(worker, i)

    tp.wait()
    tp.busy()
