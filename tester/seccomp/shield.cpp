/*
 * seccomp example with syscall reporting
 *
 * Copyright (c) 2012 The Chromium OS Authors <chromium-os-dev@chromium.org>
 * Authors:
 *  Kees Cook <keescook@chromium.org>
 *  Will Drewry <wad@chromium.org>
 *
 * Use of this source code is governed by a BSD-style license that can be
 * found in the LICENSE file.
 */

#include "def.h"

#define _GNU_SOURCE 1
#include <stdio.h>
#include <stddef.h>
#include <stdlib.h>
#include <unistd.h>

#include "config.h"
#include "seccomp-bpf.h"

// Diet-libc doesn't define PR_SET_SECCOMP
#ifndef PR_SET_SECCOMP
#  define PR_SET_SECCOMP 22
#endif

static int install_syscall_filter(void)
{
	struct sock_filter filter[] = {
		/* Validate architecture. */
		VALIDATE_ARCHITECTURE,
		/* Grab the system call number. */
		EXAMINE_SYSCALL,
		/* List allowed syscalls. */
		ALLOW_SYSCALL(rt_sigreturn),
#ifdef __NR_sigreturn
		ALLOW_SYSCALL(sigreturn),
#endif
		ALLOW_SYSCALL(exit_group),
		ALLOW_SYSCALL(exit),
		ALLOW_SYSCALL(read),
		ALLOW_SYSCALL(write),
		/* Add missing syscalls here. */
#include "missing_syscalls.h"
		KILL_PROCESS,
	};
	struct sock_fprog prog = {
		.len = (unsigned short)(sizeof(filter)/sizeof(filter[0])),
		.filter = filter,
	};

	if (prctl(PR_SET_NO_NEW_PRIVS, 1, 0, 0, 0)) {
		perror("prctl(NO_NEW_PRIVS)");
		//goto failed;
		if (errno == EINVAL)
			fprintf(stderr, "SECCOMP_FILTER is not available. :(\n");
		return 1;
	}
	if (prctl(PR_SET_SECCOMP, SECCOMP_MODE_FILTER, &prog)) {
		perror("prctl(SECCOMP)");
		//goto failed;
		if (errno == EINVAL)
			fprintf(stderr, "SECCOMP_FILTER is not available. :(\n");
		return 1;
	}
	return 0;

failed:
	if (errno == EINVAL)
		fprintf(stderr, "SECCOMP_FILTER is not available. :(\n");
	return 1;
}

int themainmainfunction();

int main(int argc, char *argv[])
{
	if (install_syscall_filter())
		return 1;


	themainmainfunction();
	
	return 0;
}

#include "code.c"