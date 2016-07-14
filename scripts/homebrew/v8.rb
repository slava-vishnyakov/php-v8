# Track Chrome stable.
# https://omahaproxy.appspot.com/
class V8 < Formula
  desc "Google's JavaScript engine"
  homepage "https://code.google.com/p/v8/"
  url "https://chromium.googlesource.com/v8/v8.git/+archive/5.2.371.tar.gz"
  sha256 "526134c9f543def42e96ab55fb9ef4b0f5f741aa755d9dad59cad1120dcda0f7"
  head "https://chromium.googlesource.com/v8/v8.git"

  bottle do
    cellar :any
  end

  option "with-readline", "Use readline instead of libedit"

  # not building on Snow Leopard:
  # https://github.com/Homebrew/homebrew/issues/21426
  depends_on :macos => :lion

  depends_on :python => :build # gyp doesn't run under 2.6 or lower
  depends_on "readline" => :optional
  depends_on "icu4c" => :recommended

  needs :cxx11

  # Update from "DEPS" file in tarball.

  # resources definition, do not edit, autogenerated

  resource "mozilla-tests" do
    url "https://chromium.googlesource.com/v8/deps/third_party/mozilla-tests.git",
    :revision => "f6c578a10ea707b1a8ab0b88943fe5115ce2b9be"
  end

  resource "buildtools" do
    url "https://chromium.googlesource.com/chromium/buildtools.git",
    :revision => "06e80a0e17319868d4a9b13f9bb6a248dc8d8b20"
  end

  resource "ecmascript_simd" do
    url "https://chromium.googlesource.com/external/github.com/tc39/ecmascript_simd.git",
    :revision => "c8ef63c728283debc25891123eb00482fee4b8cd"
  end

  resource "googlemock" do
    url "https://chromium.googlesource.com/external/googlemock.git",
    :revision => "0421b6f358139f02e102c9c332ce19a33faf75be"
  end

  resource "clang" do
    url "https://chromium.googlesource.com/chromium/src/tools/clang.git",
    :revision => "996bab489f816e51dde704bd215fb3403919f07e"
  end

  resource "googletest" do
    url "https://chromium.googlesource.com/external/github.com/google/googletest.git",
    :revision => "6f8a66431cb592dad629028a50b3dd418a408c87"
  end

  resource "common" do
    url "https://chromium.googlesource.com/chromium/src/base/trace_event/common.git",
    :revision => "54b8455be9505c2cb0cf5c26bb86739c236471aa"
  end

  resource "benchmarks" do
    url "https://chromium.googlesource.com/v8/deps/third_party/benchmarks.git",
    :revision => "05d7188267b4560491ff9155c5ee13e207ecd65f"
  end

  resource "gyp" do
    url "https://chromium.googlesource.com/external/gyp.git",
    :revision => "bce1c7793010574d88d7915e2d55395213ac63d1"
  end

  resource "test262" do
    url "https://chromium.googlesource.com/external/github.com/tc39/test262.git",
    :revision => "9c45e2ac684bae64614d8eb55789cae97323a7e7"
  end

  resource "swarming" do
    url "https://chromium.googlesource.com/external/swarming.client.git",
    :revision => "df6e95e7669883c8fe9ef956c69a544154701a49"
  end

  resource "icu" do
    url "https://chromium.googlesource.com/chromium/deps/icu.git",
    :revision => "c291cde264469b20ca969ce8832088acb21e0c48"
  end

  resource "build" do
    url "https://chromium.googlesource.com/chromium/src/build.git",
    :revision => "b2d15686436cdc17f67c3621c314f8d96b5b6fd9"
  end

  def install
    # Bully GYP into correctly linking with c++11
    ENV.cxx11
    ENV["GYP_DEFINES"] = "clang=1 mac_deployment_target=#{MacOS.version}"
    # https://code.google.com/p/v8/issues/detail?id=4511#c3
    ENV.append "GYP_DEFINES", "v8_use_external_startup_data=0"

    if build.with? "icu4c"
      ENV.append "GYP_DEFINES", "use_system_icu=1"
      i18nsupport = "i18nsupport=on"
    else
      i18nsupport = "i18nsupport=off"
    end

    # fix up libv8.dylib install_name
    # https://github.com/Homebrew/homebrew/issues/36571
    # https://code.google.com/p/v8/issues/detail?id=3871
    inreplace "src/v8.gyp",
              "'OTHER_LDFLAGS': ['-dynamiclib', '-all_load']",
              "\\0, 'DYLIB_INSTALL_NAME_BASE': '#{opt_lib}'"

    # fix gyp: Error importing pymod_do_mainmodule (detect_v8_host_arch): No module named detect_v8_host_arch
    # https://groups.google.com/forum/#!topic/v8-dev/T8CTU6n5EQw
    inreplace "Makefile",
              'PYTHONPATH="$(shell pwd)/tools/generate_shim_headers:$(shell pwd)/build:$(PYTHONPATH):$(shell pwd)/tools/gyp/pylib:$(PYTHONPATH)"',
              'PYTHONPATH="$(shell pwd)/tools/generate_shim_headers:$(shell pwd)/build:$(shell pwd)/gypfiles:$(PYTHONPATH):$(shell pwd)/tools/gyp/pylib:$(PYTHONPATH)"'

    # resources installation, do not edit, autogenerated
    (buildpath/"test/mozilla/data").install resource("mozilla-tests")
    (buildpath/"buildtools").install resource("buildtools")
    (buildpath/"test/simdjs/data").install resource("ecmascript_simd")
    (buildpath/"testing/gmock").install resource("googlemock")
    (buildpath/"tools/clang").install resource("clang")
    (buildpath/"testing/gtest").install resource("googletest")
    (buildpath/"base/trace_event/common").install resource("common")
    (buildpath/"test/benchmarks/data").install resource("benchmarks")
    (buildpath/"tools/gyp").install resource("gyp")
    (buildpath/"test/test262/data").install resource("test262")
    (buildpath/"tools/swarming_client").install resource("swarming")
    (buildpath/"third_party/icu").install resource("icu")
    (buildpath/"build").install resource("build")

    system "make", "native", "library=shared", "snapshot=on",
                   "console=readline", i18nsupport,
                   "strictaliasing=off"

    include.install Dir["include/*"]

    cd "out/native" do
      rm ["libgmock.a", "libgtest.a"]
      lib.install Dir["lib*"]
      bin.install "d8", "mksnapshot", "process", "v8_shell" => "v8"
    end
  end

  test do
    assert_equal "Hello World!", pipe_output("#{bin}/v8 -e 'print(\"Hello World!\")'").chomp
  end
end